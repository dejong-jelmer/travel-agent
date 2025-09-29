import { onMounted, watch, computed } from "vue";
import { emailRegex, phoneRegex, postalRegex } from "@/validators/regex.js";
import { useForm } from "@inertiajs/vue3";
import { LogOut } from "lucide-vue-next";

const birthdateConstraint = new Date(
    new Date().setFullYear(new Date().getFullYear() - 12)
);
const maxDate = new Date(new Date().setFullYear(new Date().getFullYear() + 1));

const createTraveler = () => {
    const traveler = {
        first_name: "Test",
        last_name: "de Tester",
        birthdate: new Date("1950-12-01"),
        nationality: "NL",
        get full_name() {
            return `${this.first_name} ${this.last_name}`;
        },
    };

    return traveler;
};

/**
 * @typedef {Object} BookingState
 * @property {Object} trip - Instance of Product (Trip)
 * @property {Object} participants - Number of adults/children that will participate
 * @property {Date} departure_date - Selected Date
 * @property {Object} travelers - Traveler details
 * @property {Object} contact - Contact details
 * @property {Object} main_booker - The array index key of travelers.adults as the main booker
 * @property {Object|null} constrains - Constains to use in the booking process
 * @param {Object} db_booking - The booking from the database
 * @param {integer} main_booker_index - The array key index for the main booker booking.travelers.adults
 */

export function useBooking(trip, db_booking, main_booker_index) {
    if (!trip) {
        console.warn("useBooking: trip parameter is required");
    }

    const adults = db_booking?.travelers
        ? db_booking.travelers.filter((tr) => tr.type == "adult")
        : null;
    const children = db_booking?.travelers
        ? db_booking.travelers.filter((tr) => tr.type == "child")
        : null;

    const booking = useForm({
        trip: trip || null,
        participants: {
            adults: adults?.length ?? 1,
            children: children?.length ?? 0,
        },
        departure_date: new Date(),
        travelers: {
            adults: adults ?? [],
            children: children ?? [],
        },
        contact: db_booking?.contact || {
            street: "Test",
            house_number: 20,
            addition: "A",
            postal: "1234AB",
            city: "Test",
            email: "test@test.nl",
            phone: "+31612345678",
        },
        main_booker: main_booker_index ?? 0,
        constrains: {
            birthdate: birthdateConstraint,
            maxDate: maxDate,
        },
        confirmed: true,
    });

    function registerClearErrors(form) {
        function walk(obj, path = []) {
            if (obj === null || typeof obj !== "object") return;

            if (Array.isArray(obj)) {
                // Watch de hele array, zodat nieuwe items ook mee gaan
                watch(
                    () => obj.slice(), // shallow copy zodat Vue de verandering detecteert
                    (newVal) => {
                        newVal.forEach((item, index) => {
                            walk(item, [...path, index]);
                        });
                    },
                    { immediate: true, deep: true }
                );
            } else {
                Object.keys(obj).forEach((key) => {
                    const newPath = [...path, key];
                    const value = obj[key];

                    if (value !== null && typeof value === "object") {
                        walk(value, newPath);
                    } else {
                        watch(
                            () => obj[key],
                            () => form.clearErrors(newPath.join(".")),
                            { immediate: false }
                        );
                    }
                });
            }
        }

        walk(form);
    }

    onMounted(() => {
        registerClearErrors(booking);
    });

    function syncTravelers(type) {
        const newCount = booking.participants[type];
        const current = booking.travelers[type].length;

        if (newCount > current) {
            for (let i = current; i < newCount; i++) {
                booking.travelers[type].push(createTraveler());
            }
        } else {
            booking.travelers[type].splice(newCount);
        }
    }

    watch(
        () => booking.participants,
        () => {
            Object.keys(booking.participants).forEach((type) => {
                syncTravelers(type);
            });
        },
        { immediate: true }
    );
    watch(
        () => booking.travelers.adults.length,
        () => {
            booking.main_booker = main_booker_index ?? 0;
        },
        { immediate: true }
    );

    function makeValidator(path, rule, message) {
        return {
            hasError: () => !!booking.errors[path],
            message: () =>
                booking.errors[path] ??
                (rule() ? booking.setError(path, message).errors[path] : null),
        };
    }

    function buildTravelerValidators(type, travelers) {
        return travelers.map((t, i) => ({
            first_name: makeValidator(
                `travelers.${type}.${i}.first_name`,
                () => t.first_name.length < 3,
                "Voornaam is verplicht."
            ),
            last_name: makeValidator(
                `travelers.${type}.${i}.last_name`,
                () => t.last_name.length < 3,
                "Achternaam is verplicht."
            ),
            birthdate: makeValidator(
                `travelers.${type}.${i}.birthdate`,
                () => t.birthdate === null,
                "Geboortedatum is verplicht."
            ),
            nationality: makeValidator(
                `travelers.${type}.${i}.nationality`,
                () => t.nationality.length < 2,
                "Nationaliteit is verplicht."
            ),
        }));
    }

    const validator = computed(() => {
        return {
            departure_date: makeValidator(
                "departure_date",
                () => booking.departure_date === null,
                "Kies een geldige vertrekdatum."
            ),
            contact: {
                street: makeValidator(
                    "contact.street",
                    () => booking.contact.street.length < 3,
                    "De straatnaam is verplicht."
                ),
                house_number: makeValidator(
                    "contact.house_number",
                    () => booking.contact.house_number < 1,
                    "Het huisnummer is verplicht."
                ),
                addition: makeValidator("contact.addition", () => null, null),
                postal: makeValidator(
                    "contact.postal",
                    () => !postalRegex.test(booking.contact.postal),
                    "Voer een geldige postcode in."
                ),
                city: makeValidator(
                    "contact.city",
                    () => booking.contact.city.length < 3,
                    "Een plaatsnaam is verplicht."
                ),
                email: makeValidator(
                    "contact.email",
                    () => !emailRegex.test(booking.contact.email),
                    "Voer een geldig e-mailadres in."
                ),
                phone: makeValidator(
                    "contact.phone",
                    () => !phoneRegex.test(booking.contact.phone),
                    "Voer een geldig telefoonnummer in."
                ),
            },
            travelers: {
                adults: buildTravelerValidators(
                    "adults",
                    booking.travelers.adults
                ),
                children: buildTravelerValidators(
                    "children",
                    booking.travelers.children
                ),
            },
        };
    });

    return { booking, validator };
}
