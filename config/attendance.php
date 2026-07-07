<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify the coordinates of your church for GPS-based
    | attendance verification, as well as the maximum allowed radius
    | (in meters) for a member to be considered "at the church".
    |
    */

    'church_latitude' => env('ATTENDANCE_CHURCH_LAT', -6.924585),
    'church_longitude' => env('ATTENDANCE_CHURCH_LNG', 107.621815),
    'max_radius_meters' => env('ATTENDANCE_MAX_RADIUS', 75),

];
