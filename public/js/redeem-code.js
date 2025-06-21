/**
 * Redeem Code JavaScript Functions
 * Functions for handling redeem code validation and usage
 */

class RedeemCodeManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Validate a redeem code
     * @param {string} code - The redeem code to validate
     * @param {number} amount - The purchase amount
     * @returns {Promise} - Promise with validation result
     */
    async validateCode(code, amount) {
        try {
            const response = await fetch('/redeem-code/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    amount: amount
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error validating redeem code:', error);
            return {
                success: false,
                message: 'Terjadi kesalahan saat memvalidasi kode'
            };
        }
    }

    /**
     * Use a redeem code
     * @param {string} code - The redeem code to use
     * @param {number} amount - The purchase amount
     * @returns {Promise} - Promise with usage result
     */
    async useCode(code, amount) {
        try {
            const response = await fetch('/redeem-code/use', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    amount: amount
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error using redeem code:', error);
            return {
                success: false,
                message: 'Terjadi kesalahan saat menggunakan kode'
            };
        }
    }

    /**
     * Get available redeem codes
     * @returns {Promise} - Promise with available codes
     */
    async getAvailableCodes() {
        try {
            const response = await fetch('/redeem-code/available', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting available codes:', error);
            return {
                success: false,
                message: 'Terjadi kesalahan saat mengambil kode yang tersedia'
            };
        }
    }

    /**
     * Show success message
     * @param {string} message - Success message
     */
    showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert('Berhasil: ' + message);
        }
    }

    /**
     * Show error message
     * @param {string} message - Error message
     */
    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        } else {
            alert('Error: ' + message);
        }
    }

    /**
     * Show info message
     * @param {string} message - Info message
     */
    showInfo(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: message
            });
        } else {
            alert('Info: ' + message);
        }
    }

    /**
     * Format currency
     * @param {number} amount - Amount to format
     * @returns {string} - Formatted currency
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    /**
     * Initialize redeem code input with validation
     * @param {string} inputSelector - CSS selector for the input
     * @param {string} buttonSelector - CSS selector for the validate button
     * @param {number} amount - Purchase amount
     */
    initRedeemCodeInput(inputSelector, buttonSelector, amount) {
        const input = document.querySelector(inputSelector);
        const button = document.querySelector(buttonSelector);

        if (!input || !button) return;

        button.addEventListener('click', async () => {
            const code = input.value.trim().toUpperCase();
            
            if (!code) {
                this.showError('Masukkan kode redeem');
                return;
            }

            button.disabled = true;
            button.textContent = 'Memvalidasi...';

            const result = await this.validateCode(code, amount);
            
            button.disabled = false;
            button.textContent = 'Validasi Kode';

            if (result.success) {
                this.showSuccess(`Kode valid! ${result.data.description || ''}`);
                // You can add additional logic here, like updating the UI
                return result.data;
            } else {
                this.showError(result.message);
                return null;
            }
        });

        // Allow Enter key to trigger validation
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                button.click();
            }
        });
    }
}

// Initialize global instance
window.redeemCodeManager = new RedeemCodeManager();

// Example usage:
// 
// // Initialize redeem code input
// redeemCodeManager.initRedeemCodeInput('#redeem-code-input', '#validate-button', 150000);
//
// // Validate code manually
// const result = await redeemCodeManager.validateCode('DISKON10', 150000);
// if (result.success) {
//     console.log('Discount amount:', result.data.discount_amount);
// }
//
// // Use code
// const usage = await redeemCodeManager.useCode('DISKON10', 150000);
// if (usage.success) {
//     console.log('Code used successfully');
// } 