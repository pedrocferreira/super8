function ordinal($number) {
    $ends = ['º', 'º', 'º', 'º'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'º';
    } else {
        return $number . $ends[$number % 10];
    }
}
