import React, {useState} from 'react';
import {Button, Modal} from '@gravity-ui/uikit';
import {QRCodeSVG} from 'qrcode.react';

interface QRCodeModalProps {
    title?: string;
    buttonText?: string;
    description?: string | null;
}

const QRCodeModal = ({
                         title = 'QR Code',
                         buttonText = 'QR',
                         description = null
                     }: QRCodeModalProps) => {
    const [open, setOpen] = useState(false);

    const currentUrl = window.location.href;

    const handlePrint = () => {
        const printContent = document.getElementById('printable-qr-content')?.innerHTML;
        if (!printContent) return;

        const printWindow = window.open('', '_blank');
        if (!printWindow) return;

        printWindow!.document.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>${title}</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              text-align: center;
              padding: 40px 20px;
              margin: 0;
            }
            .print-title {
              font-size: 24px;
              margin-bottom: 20px;
              font-weight: bold;
            }
            .print-description {
              margin-bottom: 30px;
              font-size: 16px;
              color: #555;
              line-height: 1.4;
              max-width: 400px;
              margin-left: auto;
              margin-right: auto;
            }
            .print-url {
              margin-top: 25px;
              word-break: break-all;
              font-size: 14px;
              color: #666;
            }
            @media print {
              body { margin: 0; padding: 30px; }
            }
          </style>
        </head>
        <body>
          ${printContent}
          <script>
            window.onload = function() {
              window.print();
              setTimeout(() => window.close(), 500);
            }
          </script>
        </body>
      </html>
    `);
        printWindow!.document.close();
    };

    return (
        <>
            <Button view="outlined" onClick={() => setOpen(true)}>
                {buttonText}
            </Button>

            <Modal open={open} onClose={() => setOpen(false)}>
                <div style={{
                    padding: '30px',
                    textAlign: 'center',
                    maxWidth: '600px', // Увеличили для большого QR-кода
                    margin: '0 auto'
                }}>
                    <div id="printable-qr-content">

                        <h2 style={{marginBottom: '16px', fontSize: '24px'}}>{title}</h2>

                        {/* Описание ТОЛЬКО над QR-кодом */}
                        {description && (
                            <p style={{
                                marginBottom: '25px',
                                color: '#555',
                                lineHeight: '1.4',
                                fontSize: '16px'
                            }}>
                                {description}
                            </p>
                        )}
                        <div style={{
                            display: 'inline-block',
                            padding: '25px',
                            backgroundColor: 'white',
                            borderRadius: '8px',
                            marginBottom: '20px'
                        }}>
                            <QRCodeSVG
                                value={currentUrl}
                                size={400} // Увеличили с 200 до 400
                                level="H"
                            />
                        </div>

                        <div className="print-url">
                            {currentUrl}
                        </div>
                    </div>

                    <Button
                        view="action"
                        onClick={handlePrint}
                        style={{marginTop: '25px'}}
                        size="l"
                    >
                        Печать
                    </Button>
                </div>
            </Modal>
        </>
    );
};

export default QRCodeModal;
