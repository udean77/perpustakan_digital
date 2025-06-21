# Setup Midtrans Payment Integration

## Overview
This document explains how to set up Midtrans payment gateway integration for the PustakaDigital e-commerce application.

## Prerequisites
1. Midtrans account (https://midtrans.com)
2. Laravel application with the Midtrans PHP library installed

## Installation

### 1. Install Midtrans PHP Library
```bash
composer require midtrans/midtrans-php
```

### 2. Configuration

#### Environment Variables
Add the following variables to your `.env` file:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_MERCHANT_ID=your_merchant_id_here
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_3DS=true
```

#### Configuration File
The configuration is stored in `config/midtrans.php` and includes:
- Server and Client keys
- Environment settings (sandbox/production)
- 3D Secure settings
- API URLs

### 3. Service Class
The `MidtransService` class (`app/Services/MidtransService.php`) handles:
- Creating Snap tokens for payment
- Processing payment notifications
- Verifying payment status
- Handling item details and discounts

### 4. Payment Controller
The `PaymentController` (`app/Http/Controllers/PaymentController.php`) manages:
- Payment creation and Snap token generation
- Payment callback handling (success, error, pending)
- Payment notification processing

## Usage

### 1. Creating a Payment
After checkout, users are redirected to:
```
/payment/{orderId}
```

This creates a Snap token and displays the payment page.

### 2. Payment Flow
1. User completes checkout
2. System creates order and redirects to payment page
3. Midtrans Snap popup appears with payment options
4. User selects payment method and completes payment
5. System receives notification and updates order status

### 3. Supported Payment Methods
- Credit Card
- Bank Transfer (BCA, BNI, BRI)
- E-Wallet (GoPay)
- Convenience Store (Indomaret)
- Installment (Danamon Online, Akulaku)

## Callback URLs

### Success Callback
```
/payment/finish
```
Handles successful payments and updates order status to 'selesai'.

### Error Callback
```
/payment/error
```
Handles failed payments and updates order status to 'dibatalkan'.

### Pending Callback
```
/payment/pending
```
Handles pending payments and updates order status to 'pending'.

### Notification URL
```
/payment/notification
```
Receives payment status updates from Midtrans (POST request).

## Testing

### Sandbox Environment
For testing, use Midtrans sandbox environment:
- Set `MIDTRANS_IS_PRODUCTION=false`
- Use sandbox credentials from Midtrans dashboard
- Test with sandbox payment methods

### Test Cards
Use Midtrans test cards for credit card testing:
- Visa: 4811 1111 1111 1114
- Mastercard: 5211 1111 1111 1117
- JCB: 3566 0020 2036 0505

### Test Bank Accounts
For bank transfer testing, use the virtual account numbers provided by Midtrans.

## Production Deployment

### 1. Update Environment
Set production environment:
```env
MIDTRANS_IS_PRODUCTION=true
```

### 2. Update Credentials
Replace sandbox credentials with production credentials:
- Server Key
- Client Key
- Merchant ID

### 3. Configure Notification URL
In Midtrans dashboard, set the notification URL to:
```
https://yourdomain.com/payment/notification
```

### 4. SSL Certificate
Ensure your domain has a valid SSL certificate for secure payment processing.

## Security Considerations

### 1. Signature Verification
The system verifies payment notifications using signature keys to prevent fraud.

### 2. Order Validation
Orders are validated to ensure they belong to the authenticated user.

### 3. Error Handling
Comprehensive error handling and logging for payment failures.

## Troubleshooting

### Common Issues

1. **Snap Token Creation Failed**
   - Check server key configuration
   - Verify order data format
   - Check network connectivity

2. **Payment Notifications Not Received**
   - Verify notification URL configuration
   - Check server logs for errors
   - Ensure proper signature verification

3. **Order Status Not Updated**
   - Check notification handling
   - Verify database connections
   - Review error logs

### Debug Mode
Enable debug logging by adding to `.env`:
```env
LOG_LEVEL=debug
```

## Support
For Midtrans-specific issues, contact Midtrans support.
For application integration issues, check the application logs and documentation. 