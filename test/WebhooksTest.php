<?php

namespace Tebex;

use PHPUnit\Framework\TestCase;
use Tebex\Webhook\PaymentCompletedWebhook;
use Tebex\Webhook\Webhook;
use TebexCheckout\ApiException;
use TebexCheckout\Model\PaymentSubject;
use ValueError;
use const Tebex\Webhook\PAYMENT_COMPLETED;

class WebhooksTest extends TestCase
{
    protected function setUp(): void {
        $_SERVER["HTTP_X_SIGNATURE"] = (string)getenv("TEBEX_HEADLESS_WEBHOOK_SIGNATURE");
        $_SERVER["REMOTE_ADDR"] = "18.209.80.3";
    }

    public function testSetSecretKey()
    {
        Webhooks::setSecretKey("abc123");
        self::assertTrue(Webhooks::isSecretKeySet());
    }

    public function testValidateWebhookSignature()
    {
        Webhooks::setSecretKey("abc123");

        $_SERVER["HTTP_X_SIGNATURE"] = (string)getenv("TEBEX_WEBHOOK_TEST_SIGNATURE");
        $_SERVER["REMOTE_ADDR"] = "18.209.80.3";
        $hook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        $validated = Webhooks::validateWebhookSignature($hook, $_SERVER["HTTP_X_SIGNATURE"]);
        self::assertTrue($validated);
    }

    public function testInvalidWebhookSignature() {
        Webhooks::setSecretKey("abc123");

        // invalid signature
        $_SERVER["HTTP_X_SIGNATURE"] = "INVALID_TEST_SIGNATURE";
        $_SERVER["REMOTE_ADDR"] = "18.209.80.3";

        $this->expectException(ApiException::class);
        $hook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        $validated = Webhooks::validateWebhookSignature($hook, $_SERVER["HTTP_X_SIGNATURE"]);
        self::assertNotTrue($validated);
    }

    public function testInvalidWebhookIP() {
        Webhooks::setSecretKey("abc123");

        $_SERVER["HTTP_X_SIGNATURE"] = "c808d8ddb5e64eeccb96716a0c5023053eb14a3393b06b91387416db2784f06e";
        $_SERVER["REMOTE_ADDR"] = "127.0.0.1";

        $this->expectException(ApiException::class);
        $hook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        $validated = Webhooks::validateWebhookSignature($hook, $_SERVER["HTTP_X_SIGNATURE"]);
        self::assertFalse($validated);
    }

    public function testValidIsType() {
        Webhooks::setSecretKey("abc123");
        $webhook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        self::assertTrue($webhook->isType(PAYMENT_COMPLETED));
    }

    public function testInvalidIsType() {
        self::expectException(\ValueError::class);
        $webhook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        $webhook->isType("invalid");
    }

    public function testIsStatusComplete() {
        $completedWebhook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        //$notCompletedWebhook = Webhook::parse(TestWebhooks::$examplePendingCheckoutJson);

        self::assertTrue($completedWebhook->isStatusComplete());
        //self::assertNotTrue($notCompletedWebhook->isStatusCompleted());
    }
    public function testMissingWebhookIP() {
        self::expectException(\ValueError::class);
        self::expectExceptionMessage("IP header not present");
        Webhooks::setSecretKey("abc123");

        // correct signature
        $_SERVER["HTTP_X_SIGNATURE"] = "4a36303e29cc9ae9394e6013427f6a1be0459d5904b4f6dac96c91515eefff32";
        unset($_SERVER["REMOTE_ADDR"]);

        $hook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);
        $validated = Webhooks::validateWebhookSignature($hook, $_SERVER["HTTP_X_SIGNATURE"]);

        self::assertNotTrue($validated);
    }

    public static $exampleValidateJson = <<<json
    {
        "id": "679e12cd-b2bc-4c75-83fe-21a40f6b9f00",
        "type": "validation.webhook",
        "date": "2024-07-12T14:52:18+00:00",
        "subject": {}
    }
    json;

//    /**
//     * Test fromJson with valid 'payment.completed' webhook type.
//     */
//    public function testFromJsonValidPaymentCompleted()
//    {
//        $webhook = Webhook::parse(WebhooksTest::$exampleValidPaymentCompletedJson);
//
//        $this->assertInstanceOf(PaymentCompletedWebhook::class, $webhook);
//        $this->assertEquals('173c9b8f-56b7-48cb-82d5-213c08831e63', $webhook->getId());
//        $this->assertEquals('payment.completed', $webhook->getType());
//        $this->assertEquals('2024-08-04T07:23:03+00:00', $webhook->getDate());
//        $this->assertInstanceOf(PaymentSubject::class, $webhook->getSubject());
//
//        // Test payment subject helpers for coverage
//        $paymentSubject = $webhook->getSubject();
//        $this->assertInstanceOf(PaymentSubjectPrice::class, $paymentSubject->getPrice());
//        $this->assertIsString($paymentSubject->getTransactionId());
//        $this->assertInstanceOf(PaymentStatus::class, $paymentSubject->getStatus());
//        $this->assertIsArray($paymentSubject->getCoupons());
//        //$this->assertInstanceOf(DateTime::class, $paymentSubject->getCreatedAt());
//        $this->assertIsArray($paymentSubject->getCustom());
//        $this->assertInstanceOf(PaymentSubjectCustomer::class, $paymentSubject->getCustomer());
//        $this->assertNull($paymentSubject->getDeclineReason());
//
//        $this->assertInstanceOf(PaymentSubjectFees::class, $paymentSubject->getFees());
//        $this->assertIsArray($paymentSubject->getGiftCards());
//
//
//        $this->assertInstanceOf(PaymentSubjectPaymentMethod::class, $paymentSubject->getPaymentMethod());
//        $this->assertIsString($paymentSubject->getPaymentSequence());
//        $this->assertInstanceOf(PaymentSubjectPrice::class, $paymentSubject->getPricePaid());
//        $this->assertIsArray($paymentSubject->getProducts());
//        $this->assertIsArray($paymentSubject->getRevenueShare());
//    }

//    /**
//     * Test fromJson with valid 'recurring-payment.started' webhook type.
//     */
//    public function testFromJsonValidRecurringPaymentStarted()
//    {
//        $webhook = Webhook::parse(WebhooksTest::$exampleValidRecurringPaymentStartedJson);
//
//        $this->assertInstanceOf(RecurringPaymentStartedWebhook::class, $webhook);
//        $this->assertEquals('48ea5a8d-5fdf-4fe6-b247-4e46305a090e', $webhook->getId());
//        $this->assertEquals('recurring-payment.started', $webhook->getType());
//        $this->assertEquals('2024-07-03T19:45:31+00:00', $webhook->getDate());
//        $this->assertInstanceOf(RecurringPaymentSubject::class, $webhook->getSubject());
//
//        $paymentSubject = $webhook->getSubject();
//        $this->assertNull($paymentSubject->getCancelledAt());
//        $this->assertNull($paymentSubject->getCancelReason());
//        $this->assertIsInt($paymentSubject->getFailCount());
//        $this->assertInstanceOf(PaymentSubject::class, $paymentSubject->getInitialPayment());
//        $this->assertInstanceOf(PaymentSubject::class, $paymentSubject->getLastPayment());
//        $this->assertIsString($paymentSubject->getReference());
//    }

    /**
     * Test fromJson with invalid JSON.
     */
    public function testFromJsonInvalidJson()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Invalid or malformed webhook JSON");
        Webhook::parse(WebhooksTest::$exampleMalformedJson);
    }

    /**
     * Test fromJson with missing type field.
     */
    public function testFromJsonMissingType()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Webhook type is missing from the payload");
        Webhook::parse(WebhooksTest::$exampleMissingTypeJson);
    }

    /**
     * Test fromJson with unrecognized webhook type.
     */
    public function testFromJsonUnrecognizedType()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Unrecognized webhook type");
        Webhook::parse(WebhooksTest::$exampleNonExistingClassJson);
    }

    /**
     * Test fromJson with missing subject.
     */
    public function testFromJsonMissingSubject()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("missing subject");
        Webhook::parse(WebhooksTest::$exampleMissingSubjectJson);
    }

    /**
     * Test fromJson with invalid subject data.
     */
    public function testFromJsonInvalidSubjectData()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("subject is null");
        Webhook::parse(WebhooksTest::$jsonWithInvalidSubject);
    }

    /**
     * Test fromJson when the webhook class does not exist.
     */
    public function testFromJsonNonExistentWebhookClass()
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Unrecognized webhook type");
        Webhook::parse(WebhooksTest::$exampleNonExistingClassJson);
    }

    /**
     * Test processing a valid 'payment.completed' webhook end-to-end.
     */
    public function testValidPaymentCompletedWebhookIntegration()
    {
        $webhook = Webhook::parse(WebhooksTest::$examplePaymentCompletedJson);

        // Ensure the correct class is instantiated
        $this->assertInstanceOf(PaymentCompletedWebhook::class, $webhook);

        // Validate the webhook data
        $this->assertEquals('abc123', $webhook->getId());
        $this->assertEquals('payment.completed', $webhook->getType());
        $this->assertEquals('2023-11-20T01:27:00+00:00', $webhook->getDate());

        // Validate the subject data
        $subject = $webhook->getSubject();
        $this->assertInstanceOf(PaymentSubject::class, $subject);
        $this->assertEquals('txn_456789', $subject->getTransactionId());
        $this->assertEquals(2.16, $subject->getPrice()["amount"]);
    }

//    public function testValidationWebhook() {
//        $webhook = Webhook::parse(WebhooksTest::$exampleValidateJson);
//        $this->assertInstanceOf(ValidationWebhook::class, $webhook);
//
//        $this->assertEquals('679e12cd-b2bc-4c75-83fe-21a40f6b9f00', $webhook->getId());
//        $this->assertEquals('validation.webhook', $webhook->getType());
//        $this->assertEquals('2024-07-12T14:52:18+00:00', $webhook->getDate());
//    }

    static string $exampleValidRecurringPaymentStartedJson = <<<json
    {
    "id": "48ea5a8d-5fdf-4fe6-b247-4e46305a090e",
    "type": "recurring-payment.started",
    "date": "2024-07-03T19:45:31+00:00",
    "subject": {
        "reference": "tbx-r-729115518",
        "created_at": "2024-07-03T19:45:31.000000Z",
        "paused_at": null,
        "paused_until": null,
        "next_payment_at": "2024-08-03T19:45:31.000000Z",
        "status": {
            "id": 2,
            "description": "Active"
        },
        "initial_payment": {
            "transaction_id": "tbx-54318424a71127-04ce18",
            "status": {
                "id": 1,
                "description": "Complete"
            },
            "payment_sequence": "mixed",
            "created_at": "2024-07-03T19:45:28.000000Z",
            "price": {
                "amount": 26.68,
                "currency": "USD"
            },
            "price_paid": {
                "amount": 26.68,
                "currency": "USD"
            },
            "payment_method": {
                "name": "Test Payments",
                "refundable": true
            },
            "fees": {
                "tax": {
                    "amount": 1.98,
                    "currency": "USD"
                },
                "gateway": {
                    "amount": 0,
                    "currency": "USD"
                }
            },
            "customer": {
                "first_name": "Dusty",
                "last_name": "alexander",
                "email": "dusty.alexander@overwolf.com",
                "ip": "45.32.217.77",
                "username": null,
                "marketing_consent": false,
                "country": "US",
                "postal_code": "35077"
            },
            "products": [
                {
                    "id": 0,
                    "name": "Standard Recurring Setup Fee",
                    "quantity": 1,
                    "base_price": {
                        "amount": 2,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 2,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": {
                        "relid": 11,
                        "type": "lineitem"
                    },
                    "username": null,
                    "servers": []
                },
                {
                    "id": 0,
                    "name": "Standard Recurring (03\/07\/2024 - 02\/08\/2024)\\nExtra Thing: One Extra",
                    "quantity": 1,
                    "base_price": {
                        "amount": 22.7,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 22.7,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": {
                        "relid": 11,
                        "type": "hosting"
                    },
                    "username": null,
                    "servers": []
                }
            ],
            "coupons": [],
            "gift_cards": [],
            "recurring_payment_reference": "tbx-r-729115518",
            "custom": {
                "invoiceId": 21
            },
            "revenue_share": [],
            "decline_reason": null
        },
        "last_payment": {
            "transaction_id": "tbx-54318424a71127-04ce18",
            "status": {
                "id": 1,
                "description": "Complete"
            },
            "payment_sequence": "mixed",
            "created_at": "2024-07-03T19:45:28.000000Z",
            "price": {
                "amount": 26.68,
                "currency": "USD"
            },
            "price_paid": {
                "amount": 26.68,
                "currency": "USD"
            },
            "payment_method": {
                "name": "Test Payments",
                "refundable": true
            },
            "fees": {
                "tax": {
                    "amount": 1.98,
                    "currency": "USD"
                },
                "gateway": {
                    "amount": 0,
                    "currency": "USD"
                }
            },
            "customer": {
                "first_name": "Dusty",
                "last_name": "alexander",
                "email": "dusty.alexander@overwolf.com",
                "ip": "45.32.217.77",
                "username": null,
                "marketing_consent": false,
                "country": "US",
                "postal_code": "35077"
            },
            "products": [
                {
                    "id": 0,
                    "name": "Standard Recurring Setup Fee",
                    "quantity": 1,
                    "base_price": {
                        "amount": 2,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 2,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": {
                        "relid": 11,
                        "type": "lineitem"
                    },
                    "username": null,
                    "servers": []
                },
                {
                    "id": 0,
                    "name": "Standard Recurring (03\/07\/2024 - 02\/08\/2024)\\nExtra Thing: One Extra",
                    "quantity": 1,
                    "base_price": {
                        "amount": 22.7,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 22.7,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": {
                        "relid": 11,
                        "type": "hosting"
                    },
                    "username": null,
                    "servers": []
                }
            ],
            "coupons": [],
            "gift_cards": [],
            "recurring_payment_reference": "tbx-r-729115518",
            "custom": {
                "invoiceId": 21
            },
            "revenue_share": [],
            "decline_reason": null
        },
        "fail_count": 0,
        "price": {
            "amount": 24.52,
            "currency": "USD"
        },
        "cancelled_at": null,
        "cancel_reason": null
    }
}
json;

    static string $exampleValidPaymentCompletedJson = <<<json
    {
    "id": "173c9b8f-56b7-48cb-82d5-213c08831e63",
    "type": "payment.completed",
    "date": "2024-08-04T07:23:03+00:00",
    "subject": {
        "transaction_id": "tbx-42621624a26579-8a531b",
        "status": {
            "id": 1,
            "description": "Complete"
        },
        "payment_sequence": "recurring",
        "created_at": "2024-08-04T07:22:59.000000Z",
        "price": {
            "amount": 2.16,
            "currency": "USD"
        },
        "price_paid": {
            "amount": 2.16,
            "currency": "USD"
        },
        "payment_method": {
            "name": "Test Payments",
            "refundable": true
        },
        "fees": {
            "tax": {
                "amount": 0.16,
                "currency": "USD"
            },
            "gateway": {
                "amount": 0,
                "currency": "USD"
            }
        },
        "customer": {
            "first_name": "Dusty",
            "last_name": "Alexander",
            "email": "dusty.alexander@overwolf.com",
            "ip": "127.0.0.1",
            "username": null,
            "marketing_consent": false,
            "country": "US",
            "postal_code": null
        },
        "products": [
            {
                "id": 0,
                "name": "Standard Recurring 4 No Options (03\/07\/2024 - 02\/08\/2024)",
                "quantity": 1,
                "base_price": {
                    "amount": 2.16,
                    "currency": "USD"
                },
                "paid_price": {
                    "amount": 2.16,
                    "currency": "USD"
                },
                "variables": [],
                "expires_at": null,
                "custom": null,
                "username": null,
                "servers": []
            }
        ],
        "coupons": [],
        "gift_cards": [],
        "recurring_payment_reference": "tbx-r-729088009",
        "custom": {},
        "revenue_share": [],
        "decline_reason": null
    }
}
json;

    static string $exampleMalformedJson = <<<json
    {
        "id": "12345",
        "type": "payment.completed",
        "date": "2024-01-01",
        "subject": {},
    }
json;

    static string $exampleMissingSubjectJson = <<<json
    {
        "id": "12345",
        "type": "nonexisting.class",
        "date": "2024-01-01"
    }
json;

    static string $exampleMissingTypeJson = <<<json
    {
        "id": "12345",
        "date": "2024-01-01",
        "subject": {}
    }
json;

    static string $exampleNonExistingClassJson = <<<json
    {
        "id": "12345",
        "type": "nonexisting.class",
        "date": "2024-01-01",
        "subject": {
          "foo": "bar"
        }
    }
json;

    // subject should not be null
    static string $jsonWithInvalidSubject = <<<json
    {
        "id": "12345",
        "type": "payment.completed",
        "date": "2024-01-01",
        "subject": null 
    }
json;

    static string $examplePaymentCompletedJson = <<<json
    {
        "id": "abc123",
        "type": "payment.completed",
        "date": "2023-11-20T01:27:00+00:00",
        "subject": {
            "transaction_id": "txn_456789",
            "status": {
                "id": 1,
                "description": "Complete"
            },
            "payment_sequence": "recurring",
            "created_at": "2024-08-04T07:22:59.000000Z",
            "price": {
                "amount": 2.16,
                "currency": "USD"
            },
            "price_paid": {
                "amount": 2.16,
                "currency": "USD"
            },
            "payment_method": {
                "name": "Test Payments",
                "refundable": true
            },
            "fees": {
                "tax": {
                    "amount": 0.16,
                    "currency": "USD"
                },
                "gateway": {
                    "amount": 0,
                    "currency": "USD"
                }
            },
            "customer": {
                "first_name": "Dusty",
                "last_name": "Alexander",
                "email": "dusty.alexander@overwolf.com",
                "ip": "45.32.217.77",
                "username": null,
                "marketing_consent": false,
                "country": "US",
                "postal_code": null
            },
            "products": [
                {
                    "id": 0,
                    "name": "Standard Recurring 4 No Options (03\/07\/2024 - 02\/08\/2024)",
                    "quantity": 1,
                    "base_price": {
                        "amount": 2.16,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 2.16,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": null,
                    "username": null,
                    "servers": []
                }
            ],
            "coupons": [],
            "gift_cards": [],
            "recurring_payment_reference": "tbx-r-729088009",
            "custom": {},
            "revenue_share": [],
            "decline_reason": null
        }
    }
    json;

    static string $examplePendingCheckoutJson = <<<json
    {
        "id": "abc123",
        "type": "payment.completed",
        "date": "2023-11-20T01:27:00+00:00",
        "subject": {
            "transaction_id": "txn_456789",
            "status": {
                "id": 19,
                "description": "Pending Checkout"
            },
            "payment_sequence": "recurring",
            "created_at": "2024-08-04T07:22:59.000000Z",
            "price": {
                "amount": 2.16,
                "currency": "USD"
            },
            "price_paid": {
                "amount": 2.16,
                "currency": "USD"
            },
            "payment_method": {
                "name": "Test Payments",
                "refundable": true
            },
            "fees": {
                "tax": {
                    "amount": 0.16,
                    "currency": "USD"
                },
                "gateway": {
                    "amount": 0,
                    "currency": "USD"
                }
            },
            "customer": {
                "first_name": "Dusty",
                "last_name": "Alexander",
                "email": "dusty.alexander@overwolf.com",
                "ip": "45.32.217.77",
                "username": null,
                "marketing_consent": false,
                "country": "US",
                "postal_code": null
            },
            "products": [
                {
                    "id": 0,
                    "name": "Standard Recurring 4 No Options (03\/07\/2024 - 02\/08\/2024)",
                    "quantity": 1,
                    "base_price": {
                        "amount": 2.16,
                        "currency": "USD"
                    },
                    "paid_price": {
                        "amount": 2.16,
                        "currency": "USD"
                    },
                    "variables": [],
                    "expires_at": null,
                    "custom": null,
                    "username": null,
                    "servers": []
                }
            ],
            "coupons": [],
            "gift_cards": [],
            "recurring_payment_reference": "tbx-r-729088009",
            "custom": {},
            "revenue_share": [],
            "decline_reason": null
        }
    }
    json;
}
