<?php

//$discountAmount = $this->calculatePrice() * ($percentage / 100);
//$newPrice = $this->calculatePrice() - $discountAmount;



/*
  ধাপ ১: অ্যাবস্ট্রাক্ট ক্লাস Booking
  আমরা প্রথমে Booking নামে একটি অ্যাবস্ট্রাক্ট ক্লাস তৈরি করব। এই ক্লাসের দুটি অ্যাবস্ট্রাক্ট মেথড থাকবে calculatePrice() এবং getBookingDetails() যা ইনহেরিট করা ক্লাসগুলোতে ইমপ্লিমেন্ট করা হবে।
*/

abstract class Booking {
    protected $customerName;
    protected $bookingDate;

    public function __construct($customerName, $bookingDate) {
        $this->customerName = $customerName;
        $this->bookingDate = $bookingDate;
    }

    abstract public function calculatePrice();
    abstract public function getBookingDetails();
}

/*
  ধাপ ২: ইন্টারফেস Discountable
  একটি Discountable ইন্টারফেস তৈরি করা হচ্ছে, যেখানে applyDiscount() মেথড ডিক্লেয়ার করা হবে।
*/

interface Discountable {
    public function applyDiscount(float $percentage);
}

/*
  ধাপ ৩: ফ্লাইট বুকিং ক্লাস FlightBooking
  এখন আমরা FlightBooking ক্লাস তৈরি করব, যা Booking ক্লাস থেকে ইনহেরিট করবে এবং Discountable ইন্টারফেস ইমপ্লিমেন্ট করবে।
*/


class FlightBooking extends Booking implements Discountable {
    private $distance;
    private $passengerCount;
    private $pricePerKm = 100;

    public function __construct($customerName, $bookingDate, $distance, $passengerCount) {
        parent::__construct($customerName, $bookingDate);
        $this->distance = $distance;
        $this->passengerCount = $passengerCount;
    }

    public function calculatePrice() {
        return $this->distance * $this->pricePerKm * $this->passengerCount;
    }

    public function getBookingDetails() {
        return [
            'customerName' => $this->customerName,
            'bookingDate' => $this->bookingDate,
            'distance' => $this->distance,
            'passengerCount' => $this->passengerCount,
            'price' => $this->calculatePrice()
        ];
    }

    public function applyDiscount(float $percentage) {
        $discountAmount = $this->calculatePrice() * ($percentage / 100);
        $newPrice = $this->calculatePrice() - $discountAmount;
        return $newPrice;
    }
}

/*
  ধাপ ৪: হোটেল বুকিং ক্লাস HotelBooking
  একইভাবে, আমরা HotelBooking ক্লাস তৈরি করব যা Booking থেকে ইনহেরিট করবে এবং Discountable ইন্টারফেস ইমপ্লিমেন্ট করবে।
*/

class HotelBooking extends Booking implements Discountable {
    private $nights;
    private $pricePerNight = 100;

    public function __construct($customerName, $bookingDate, $nights) {
        parent::__construct($customerName, $bookingDate);
        $this->nights = $nights;
    }

    public function calculatePrice() {
        return $this->nights * $this->pricePerNight;
    }

    public function getBookingDetails() {
        return [
            'customerName' => $this->customerName,
            'bookingDate' => $this->bookingDate,
            'nights' => $this->nights,
            'price' => $this->calculatePrice()
        ];
    }

    public function applyDiscount(float $percentage) {
        $discountAmount = $this->calculatePrice() * ($percentage / 100);
        $newPrice = $this->calculatePrice() - $discountAmount;
        return $newPrice;
    }
}

/*
  ধাপ ৫: কার রেন্টাল বুকিং ক্লাস CarRentalBooking
  আমরা CarRentalBooking ক্লাস তৈরি করব যা শুধুমাত্র Booking থেকে ইনহেরিট করবে, কিন্তু ডিসকাউন্ট প্রয়োগ করবে না।
*/

class CarRentalBooking extends Booking {
    private $days;
    private $pricePerDay = 50;

    public function __construct($customerName, $bookingDate, $days) {
        parent::__construct($customerName, $bookingDate);
        $this->days = $days;
    }

    public function calculatePrice() {
        return $this->days * $this->pricePerDay;
    }

    public function getBookingDetails() {
        return [
            'customerName' => $this->customerName,
            'bookingDate' => $this->bookingDate,
            'days' => $this->days,
            'price' => $this->calculatePrice()
        ];
    }
}

/*
  ধাপ ৬: BookingManager ক্লাস
  BookingManager ক্লাস তৈরি করা হবে যা সমস্ত বুকিং পরিচালনা করবে (যেমন বুকিং এড করা এবং দেখানো)।
*/

class BookingManager {
    private $bookings = [];

    public function addBooking(Booking $booking) {
        $this->bookings[] = $booking;
    }

    public function showAllBookings() {
        foreach ($this->bookings as $booking) {
            echo '<pre>';
            print_r($booking->getBookingDetails());
            echo '</pre> <br/>';
        }
    }
}

/*
  ধাপ ৭: কোড প্রয়োগ ও আউটপুট
  আমরা এখন আমাদের সিস্টেমটি চালিয়ে দেখব কিভাবে এটি কাজ করে।
*/

$bookingManager = new BookingManager();

// Flight Booking
$flightBooking = new FlightBooking('Ruhin', '2024-09-28', 500, 2);
$bookingManager->addBooking($flightBooking);

// Hotel Booking
$hotelBooking = new HotelBooking('Tonmoy', '2024-09-28', 5);
$bookingManager->addBooking($hotelBooking);

// Car Rental Booking
$carRentalBooking = new CarRentalBooking('Tuhin', '2024-09-28', 7);
$bookingManager->addBooking($carRentalBooking);

// Show All Bookings
$bookingManager->showAllBookings();

echo "Flight Price after Discount: " . $flightBooking->applyDiscount(10) . "<br/>";
echo "Hotel Price after Discount: " . $hotelBooking->applyDiscount(15) . "<br/>";

// Show All Bookings
$bookingManager->showAllBookings();

?>