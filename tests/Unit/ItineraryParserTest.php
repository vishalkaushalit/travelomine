<?php

class ItineraryParserTest extends TestCase
{
    public function test_parses_round_trip_correctly()
    {
        $input = "1 VS 26 J 12JUN 4 LHRJFK HK1   815A 810P   *1A/E*\n".
                 '2 VS 25 J 19JUN 2 JFKLHR HK1   730A 1055P  *1A/E*';

        $parser = new ItineraryParserController;
        $result = $parser->parseItineraryForTesting($input);

        $this->assertEquals('roundtrip', $result['flight_type']);
        $this->assertCount(2, $result['segments']);
        $this->assertEquals('LHR', $result['segments'][0]['from_city']);
        $this->assertEquals('JFK', $result['segments'][0]['to_city']);
    }

    public function test_handles_connecting_flights()
    {
        // Test 3-segment multicity
    }

    public function test_time_conversions()
    {
        // Test various time formats
    }
}
