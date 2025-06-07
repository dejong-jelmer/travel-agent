const emailLinks = function initEmailLinks(encodedEmail) {
    const emailElements = document.querySelectorAll('a.email-field');
    const events = ['mouseover', 'focus', 'touchstart', 'click'];

    const decodeEmailAddress = () => {
        try {
            const encoded = encodedEmail.replace(/(..)/g, '%$1');
            const decoded = decodeURIComponent(encoded);
            const reversed = decoded.split('').reverse().join('');
            const parts = reversed.split("!");

            if (parts.length !== 3) return;
            return `${parts[2]}@${parts[0]}.${parts[1]}`;
        } catch (e) {
            console.error("Fout bij decoderen e-mail:", e);
            return null;
        }
    };

    emailElements.forEach(el => {
        const handler = () => {
            const email = decodeEmailAddress();
            if (!email) return;

            el.setAttribute('href', `mailto:${email}`);
            for (let event of events) {
                el.removeEventListener(event, handler);
            }
        };
        for (let event of events) {
            el.addEventListener(event, handler);
        }
    });
}

const phoneLinks = function initPhoneLinks(encodedPhone) {
    const telElements = document.querySelectorAll('a.tel-field');
    const events = ['mouseover', 'focus', 'touchstart', 'click'];
    const decodeEmailPhone = () => {

        try {

            const encoded = encodedPhone;

            let scrambled = '';
            for (let i = 0; i < encoded.length; i += 2) {
                scrambled += String.fromCharCode(parseInt(encoded.substr(i, 2), 16));
            }
            return scrambled.split('').reverse().join('');
        } catch (e) {
            console.error("Fout bij decoderen telefoon:", e);
            return null;
        }
    }

    telElements.forEach(el => {
        const phone = decodeEmailPhone();
        el.textContent = formatPhone(phone);
    });

    telElements.forEach(el => {
        const handler = () => {
            const phone = decodeEmailPhone();

            if (!phone) return;

            el.setAttribute('href', `tel:${phone}`);
            for (let event of events) {
                el.removeEventListener(event, handler);
            }
        };
        for (let event of events) {
            el.addEventListener(event, handler);
        }
    });
}

function formatPhone(tel) {
    return tel.replace(/^(\+31)(6)(\d{2})(\d{2})(\d{2})(\d{2})$/, '$1 $2 $3 $4 $5 $6');
}


export {
    emailLinks,
    phoneLinks
}
