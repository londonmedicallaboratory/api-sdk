<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment\Strategy;

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
use function str_replace;
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

        $cardExpirationDate = $this->trim($payment->cardExpirationDate ?? throw new RuntimeException());
        $splitCardExpire = sscanf($cardExpirationDate, '%d/%d');
        $month = $splitCardExpire[0] ?? throw new RuntimeException();
        $year = $splitCardExpire[1] ?? throw new RuntimeException();
        $billingAddress = $payment->billingAddress ?? throw new RuntimeException();
        $cardNumber = $this->trim($payment->cardNumber ?? throw new RuntimeException());

        $card = new CreditCard([
            'firstName' => $payment->cardFirstName ?? throw new RuntimeException(),
            'lastName' => $payment->cardLastName ?? throw new RuntimeException(),
            'number' => $cardNumber,
            'cvv' => $payment->cardCVV ?? throw new RuntimeException(),
            'expiryMonth' => $month,
            'expiryYear' => $year,
            'address1' => $billingAddress->getAddressLine1(),
            'city' => $billingAddress->getCity(),
            'postcode' => $billingAddress->getPostalCode(),
            'country' => 'GB',
        ]);

        $gateway->createCard([
            'currency' => 'GBP',
            'card' => $card,
        ]);

        $info = $this->informationBooth->getWebsiteInfo();
        $description = sprintf('%s Test', $info['name']);

        $price = $payment->price ?? throw new RuntimeException();
        $amount = number_format($price->getAmount() / 100, 2);
        $requestMessage = $gateway->authorize([
            'amount' => $amount,
            'currency' => 'GBP',
            'card' => $card,
            'description' => $description,
            'transactionId' => $payment->id ?? throw new RuntimeException(),
            'returnUrl' => $payment->successUrl,
            'failureUrl' => $payment->failureUrl,
        ]);

        $responseMessage = $requestMessage->send();

        return $this->extractRedirectResponse($responseMessage);
    }

    private function createGateway(): GatewayInterface
    {
        $info = $this->informationBooth->getWebsiteInfo();
        $vendor = $info['sage_auth']['vendor'] ?? throw new RuntimeException('Vendor name must be defined');
        $encryptionKey = $info['sage_auth']['encryption_key'] ?? throw new RuntimeException();

        return Omnipay::create('SagePay\Direct')->initialize([
            'vendor' => $vendor,
            'testMode' => true,
            'encryptionKey' => $encryptionKey,
            'apply3DSecure' => 3,
        ]);
    }

    /**
     * Extract redirect response if returned by Sage
     * This fixes lack of abstraction in omnipay
     */
    private function extractRedirectResponse(ResponseInterface $responseMessage): ?Response
    {
        if ($responseMessage->isRedirect() && method_exists($responseMessage, 'getRedirectResponse')) {
            /** @var Response $redirectResponse */
            $redirectResponse = $responseMessage->getRedirectResponse();

            return $redirectResponse;
        }

        if (!$responseMessage->isSuccessful()) {
            throw new PaymentFailureException($responseMessage->getMessage());
        }

        return null;
    }

    private function trim(string $input): string
    {
        $input = trim($input);

        return str_replace(' ', '', $input);
    }
}
