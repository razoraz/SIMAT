import QRCode from 'qrcode';

window.QRCode = QRCode;

window.getQrCodeSvg = function(text, margin = 1) {
    if (!text) return '';
    try {
        let svgStr = '';
        if (window.QRCode && typeof window.QRCode.toString === 'function') {
            window.QRCode.toString(String(text), { type: 'svg', margin: margin }, (err, svg) => {
                if (!err && svg) svgStr = svg;
            });
            if (svgStr) {
                return 'data:image/svg+xml;utf8,' + encodeURIComponent(svgStr);
            }
        }
    } catch (e) {
        console.warn('QR SVG generation error:', e);
    }
    return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text);
};
