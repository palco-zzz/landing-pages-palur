/**
 * Thermal Receipt Printer Composable
 * Handles printing receipts to 58mm thermal printers via browser print dialog
 */

interface ReceiptItem {
    name: string;
    qty: number;
    price: number;
    subtotal: number;
}

interface ReceiptData {
    type: 'kitchen' | 'customer';
    title: string;
    subtitle?: string | null;
    store_name: string;
    date: string;
    cashier: string;
    customer_name: string;
    order_number: string;
    items: ReceiptItem[];
    total: number;
    payment_method?: string | null;
    cash_received?: number | null;
    change?: number | null;
}

export function usePrinter() {
    const formatPrice = (amount: number): string => {
        return new Intl.NumberFormat('id-ID').format(amount);
    };

    const generateReceiptHTML = (data: ReceiptData): string => {
        const isKitchen = data.type === 'kitchen';

        const itemsHTML = data.items.map(item => `
            <tr>
                <td style="text-align: left;">${item.name}</td>
                <td style="text-align: center;">${item.qty}x</td>
                <td style="text-align: right;">${formatPrice(item.subtotal)}</td>
            </tr>
        `).join('');

        const paymentHTML = data.type === 'customer' && data.payment_method ? `
            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #000;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Metode:</span>
                    <span style="font-weight: bold;">${data.payment_method.toUpperCase()}</span>
                </div>
                ${data.cash_received ? `
                    <div style="display: flex; justify-content: space-between;">
                        <span>Tunai:</span>
                        <span>Rp ${formatPrice(data.cash_received)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Kembali:</span>
                        <span style="font-weight: bold;">Rp ${formatPrice(data.change || 0)}</span>
                    </div>
                ` : ''}
            </div>
        ` : '';

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Receipt</title>
                <style>
                    @page {
                        size: 58mm auto;
                        margin: 0;
                    }
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: 'Courier New', monospace;
                        font-size: 12px;
                        width: 58mm;
                        padding: 4mm;
                        line-height: 1.4;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 8px;
                        padding-bottom: 8px;
                        border-bottom: 1px dashed #000;
                    }
                    .store-name {
                        font-size: 14px;
                        font-weight: bold;
                    }
                    .title {
                        font-size: 16px;
                        font-weight: bold;
                        margin: 4px 0;
                        ${isKitchen ? 'background: #000; color: #fff; padding: 4px;' : ''}
                    }
                    .subtitle {
                        font-size: 14px;
                        font-weight: bold;
                        color: #000;
                    }
                    .info {
                        margin-bottom: 8px;
                        font-size: 11px;
                    }
                    .customer {
                        font-size: 16px;
                        font-weight: bold;
                        text-align: center;
                        margin: 8px 0;
                        padding: 4px;
                        border: 1px solid #000;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 8px 0;
                    }
                    td {
                        padding: 2px 0;
                        font-size: 11px;
                    }
                    .total {
                        margin-top: 8px;
                        padding-top: 8px;
                        border-top: 1px dashed #000;
                        font-size: 14px;
                        font-weight: bold;
                        display: flex;
                        justify-content: space-between;
                    }
                    .footer {
                        margin-top: 12px;
                        text-align: center;
                        font-size: 10px;
                        border-top: 1px dashed #000;
                        padding-top: 8px;
                    }
                    @media print {
                        body {
                            width: 58mm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="store-name">${data.store_name}</div>
                    <div class="title">${data.title}</div>
                    ${data.subtitle ? `<div class="subtitle">${data.subtitle}</div>` : ''}
                </div>

                <div class="info">
                    <div>${data.date}</div>
                    <div>Kasir: ${data.cashier}</div>
                    <div>No: ${data.order_number}</div>
                </div>

                <div class="customer">${data.customer_name}</div>

                <table>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>

                <div class="total">
                    <span>TOTAL</span>
                    <span>Rp ${formatPrice(data.total)}</span>
                </div>

                ${paymentHTML}

                ${data.type === 'customer' ? `
                    <div class="footer">
                        <div>Terima Kasih</div>
                        <div>Selamat Menikmati</div>
                    </div>
                ` : ''}
            </body>
            </html>
        `;
    };

    const print = (receiptData: ReceiptData): void => {
        // Create or get hidden iframe
        let iframe = document.getElementById('receipt-printer-frame') as HTMLIFrameElement;

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'receipt-printer-frame';
            iframe.style.position = 'absolute';
            iframe.style.top = '-9999px';
            iframe.style.left = '-9999px';
            iframe.style.width = '58mm';
            iframe.style.height = '0';
            document.body.appendChild(iframe);
        }

        const html = generateReceiptHTML(receiptData);

        // Write to iframe and print
        const iframeDoc = iframe.contentWindow?.document;
        if (iframeDoc) {
            iframeDoc.open();
            iframeDoc.write(html);
            iframeDoc.close();

            // Wait for content to load, then print
            setTimeout(() => {
                iframe.contentWindow?.print();
            }, 250);
        }
    };

    return {
        print,
        generateReceiptHTML,
    };
}

export type { ReceiptData, ReceiptItem };
