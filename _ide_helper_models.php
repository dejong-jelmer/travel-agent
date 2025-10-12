<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property string|null $reference
 * @property int $product_id
 * @property int|null $main_booker_id
 * @property \Illuminate\Support\Carbon $departure_date
 * @property int $conditions_accepted
 * @property int $is_confirmed
 * @property int $new
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingTraveler> $adults
 * @property-read int|null $adults_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingChange> $changes
 * @property-read int|null $changes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingTraveler> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\BookingContact|null $contact
 * @property-read mixed $created_at_formatted
 * @property-read mixed $departure_date_formatted
 * @property-read \App\Models\BookingTraveler|null $mainBooker
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingTraveler> $travelers
 * @property-read int|null $travelers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking departDueNextMonth()
 * @method static \Database\Factories\BookingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking future()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking new()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereConditionsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereMainBookerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withoutTrashed()
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property int|null $user_id
 * @property string $model_type
 * @property int $model_id
 * @property string $field
 * @property string|null $old_value
 * @property string|null $new_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\Booking $booking
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingChange whereUserId($value)
 */
	class BookingChange extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property string $name
 * @property string $street
 * @property string $house_number
 * @property string|null $addition
 * @property string $postal_code
 * @property string $city
 * @property string $email
 * @property string $phone
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $address
 * @property-read \App\Models\Booking $booking
 * @method static \Database\Factories\BookingContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereAddition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingContact whereUpdatedAt($value)
 */
	class BookingContact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property \App\Enums\TravelerType $type
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon $birthdate
 * @property string $nationality
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $birthdate_formatted
 * @property-read \App\Models\Booking $booking
 * @property-read mixed $full_name
 * @method static \Database\Factories\BookingTravelerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingTraveler whereUpdatedAt($value)
 */
	class BookingTraveler extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property mixed $path
 * @property string $imageable_type
 * @property int $imageable_id
 * @property bool $featured
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $imageable
 * @method static \Database\Factories\ImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image withoutTrashed()
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string|null $location
 * @property string $description
 * @property string|null $accommodation
 * @property array<array-key, mixed>|null $activities
 * @property mixed|null $meals
 * @property mixed|null $transport
 * @property string|null $remark
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ItineraryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereAccommodation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereActivities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereMeals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereTransport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itinerary withoutTrashed()
 */
	class Itinerary extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property string|null $token
 * @property string|null $confirmation_token
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $confirmation_expires_at
 * @property string|null $unsubscribe_token
 * @property \Illuminate\Support\Carbon $subscribed_at
 * @property \Illuminate\Support\Carbon|null $unsubscribed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereConfirmationExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereConfirmationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereSubscribedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereUnsubscribeToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereUnsubscribedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsletterSubscriber whereUpdatedAt($value)
 */
	class NewsletterSubscriber extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property mixed $price
 * @property int $duration
 * @property bool $active
 * @property bool $featured
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @property-read \App\Models\Image|null $featuredImage
 * @property-read string $countries_list
 * @property-read \Illuminate\Support\Collection $image_urls
 * @property-read float $raw_price
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Itinerary> $itineraries
 * @property-read int|null $itineraries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

