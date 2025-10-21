// Composables/useDateFormatter.js
import { nextTick } from "vue";

export const useDateFormatter = () => {
    const normalizeDateFormat = (dateString) => {
        if (!dateString) return "";

        const digitsOnly = dateString.replace(/[^\d]/g, "");

        const day = digitsOnly.slice(0, 2).padStart(2, "0");
        const month = digitsOnly.slice(2, 4).padStart(2, "0");
        const year = digitsOnly.slice(4, 8);

        let formatted = day;
        if (digitsOnly.length >= 3) {
            formatted += "-" + month;
        }
        if (digitsOnly.length >= 5) {
            formatted += "-" + year;
        }

        return formatted;
    };

    const initializeBirthdate = (dateString) => {
        return normalizeDateFormat(dateString);
    };

    const formatBirthDateInput = (event, callback) => {
        const input = event.target;
        const cursorPosition = input.selectionStart;
        const oldValue = input.value;

        let digitsOnly = oldValue.replace(/[^\d]/g, "");
        digitsOnly = digitsOnly.slice(0, 8);

        let formatted = "";
        if (digitsOnly.length > 0) {
            formatted = digitsOnly.slice(0, 2);
        }
        if (digitsOnly.length >= 3) {
            formatted += "-" + digitsOnly.slice(2, 4);
        }
        if (digitsOnly.length >= 5) {
            formatted += "-" + digitsOnly.slice(4, 8);
        }
        callback(formatted);

        nextTick(() => {
            let newCursorPosition = cursorPosition;

            const dashesBefore = (
                oldValue.slice(0, cursorPosition).match(/-/g) || []
            ).length;
            const dashesAfter = (
                formatted.slice(0, cursorPosition).match(/-/g) || []
            ).length;

            newCursorPosition += dashesAfter - dashesBefore;

            if (formatted[newCursorPosition] === "-") {
                newCursorPosition++;
            }

            input.setSelectionRange(newCursorPosition, newCursorPosition);
        });
    };

    const formattedDate = (date, options = {}) => {
        const { longMonth = true, longDay = true, locale = "nl-NL" } = options;

        if (!date) return null;
        const parsedDate = new Date(date);

        if (isNaN(parsedDate)) return null;
        return parsedDate.toLocaleDateString(locale, {
            ...(longDay && { weekday: "long" }),
            day: "numeric",
            month: longMonth ? "long" : "numeric",
            year: "numeric",
        });
    };

    const isValidDate = (dateString) => {
        if (!dateString || dateString.length !== 10) return false;
        const thisYear = new Date().getFullYear();

        const [day, month, year] = dateString.split("-").map(Number);

        if (!day || !month || !year) return false;
        if (month < 1 || month > 12) return false;
        if (day < 1 || day > 31) return false;
        if (year < 1900 || year > thisYear) return false;

        // Controleer geldigheid van de datum
        const date = new Date(year, month - 1, day);
        return (
            date.getFullYear() === year &&
            date.getMonth() === month - 1 &&
            date.getDate() === day
        );
    };

    return {
        formattedDate,
        normalizeDateFormat,
        initializeBirthdate,
        formatBirthDateInput,
        isValidDate,
    };
};
