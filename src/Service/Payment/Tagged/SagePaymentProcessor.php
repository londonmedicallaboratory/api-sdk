<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment\Tagged;

use Omnipay\Omnipay;
use RuntimeException;
use LML\SDK\DTO\Payment;
use Omnipay\Common\CreditCard;
use Omnipay\Common\GatewayInterface;
use LML\SDK\Service\InformationBooth;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use LML\SDK\Exception\PaymentFailureException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use function sscanf;
use function sprintf;
use function method_exists;
use function number_format;

/**
 * @internal
 * @psalm-internal LML\SDK\Service\Payment
 */
class SagePaymentProcessor implements PaymentProcessorStrategyInterface
{
    public function __construct(
        private InformationBooth $informationBooth,
    )
    {
    }

    public static function getName(): string
    {
        return 'sage';
    }

    public function confirm(Payment $payment): ?Response
    {
        $gateway = $this->createGateway();
        $id = $payment->id ?? throw new RuntimeException('You must provide $id for payment confirmation to work.');

        $completeRequest = $gateway->completeAuthorize([
            'transactionId' => $id,
        ]);
        $message = $completeRequest->send();
        if ($response = $this->extractRedirectResponse($message)) {
            return $response;
        }

        if ($successUrl = $payment->successUrl) {
            return new RedirectResponse($successUrl);
        }

        return null;
    }

    public function pay(Payment $payment): ?Response
    {
        $gateway = $this->createGateway();

        $splitCardExpire = sscanf($payment->cardExpirationDate ?? throw new RuntimeException(), '%d/%d');
        $month = $splitCardExpire[0] ?? throw new RuntimeException();
        $year = $splitCardExpire[1] ?? throw new RuntimeException();

        $card = new CreditCard([
            'firstName'   => $payment->cardFirstName ?? throw new RuntimeException(),
            'lastName'    => $payment->cardLastName ?? throw new RuntimeException(),
            'number'      => $payment->cardNumber ?? throw new RuntimeException(),
            'cvv'         => $payment->cardCVV ?? throw new RuntimeException(),
            'expiryMonth' => $month,
            'expiryYear'  => $year,
            'address1'    => $payment->customersAddressLine1 ?? throw new RuntimeException(),
            'city'        => $payment->customersCity ?? throw new RuntimeException(),
            'postcode'    => $payment->customersPostalCode ?? throw new RuntimeException(),
            'country'     => 'GB',
        ]);

        $gateway->createCard([
            'currency' => 'GBP',
            'card'     => $card,
        ]);

        $info = $this->informationBooth->getWebsiteInfo();
        $description = sprintf('%s Test', $info['name']);

        $price = $payment->price ?? throw new RuntimeException();
        $amount = number_format($price->getAmount() / 100, 2);
        $requestMessage = $gateway->purchase([
            'amount'        => $amount,
            'currency'      => 'GBP',
            'card'          => $card,
            'description'   => $description,
            'transactionId' => $payment->id ?? throw new RuntimeException(),
            'returnUrl'     => $payment->successUrl,
            'failureUrl'    => $payment->failureUrl,
        ]);

        $responseMessage = $requestMessage->send();

        return $this->extractRedirectResponse($responseMessage);
    }

    private function createGateway(): GatewayInterface
    {
        $info = $this->informationBooth->getWebsiteInfo();
        $vendor = $info['sage_auth']['vendor'] ?? throw new RuntimeException();
        $encryptionKey = $info['sage_auth']['encryption_key'] ?? throw new RuntimeException();

        return Omnipay::create('SagePay\Direct')->initialize([
            'vendor'        => $vendor,
            'testMode'      => true,
            'encryptionKey' => $encryptionKey,
        ]);
    }

    /**
     * Extract redirect response if returned by Sage
     * This fixes lack of abstraction in omnipay
     */
    private function extractRedirectResponse(ResponseInterface $responseMessage): ?Response
    {
        if (!$responseMessage->isSuccessful()) {
            throw new PaymentFailureException($responseMessage->getMessage());
        }

        if ($responseMessage->isRedirect() && method_exists($responseMessage, 'getRedirectResponse')) {
            /** @noinspection PhpUnnecessaryLocalVariableInspection */

            /** @var Response $redirectResponse */
            $redirectResponse = $responseMessage->getRedirectResponse();

            return $redirectResponse;
        }

        return null;
    }
}
