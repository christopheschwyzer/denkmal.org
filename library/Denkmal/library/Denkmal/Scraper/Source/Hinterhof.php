<?php

class Denkmal_Scraper_Source_Hinterhof extends Denkmal_Scraper_Source_Abstract {

    public function run(DateTime $now, array $dateList) {
        $html = self::loadUrl('http://hinterhof.ch/programm/');

        return $this->processPage($html, $now);
    }

    /**
     * @param string   $html
     * @param DateTime $now
     * @return Denkmal_Scraper_EventData[]
     */
    public function processPage($html, DateTime $now) {
        $html = new CM_Dom_NodeList($html, true);
        $eventList = $html->find('#page .events > .entry');

        /** @var CM_Dom_NodeList $event */
        return Functional\map($eventList, function (CM_Dom_NodeList $event) use ($now) {
            $venueName = 'Hinterhof';
            if (false !== stripos($event->getAttribute('class'), 'dachterrasse')) {
                $venueName = 'Hinterhof Dachterrasse';
            }

            $textWeekday = $event->find('.summary .weekday')->getText();
            if (!preg_match('#^\w+ (?<day>\d+)\.(?<month>\d+) -\s*?(?<titleAndGenres>.+?)?$#u', $textWeekday, $matches)) {
                throw new CM_Exception_Invalid('Cannot parse weekday.', null, ['string' => $textWeekday]);
            }
            $title = null;
            $genres = null;
            $day = $matches['day'];
            $month = $matches['month'];
            $titleAndGenres = trim($matches['titleAndGenres']);
            if (!empty($titleAndGenres)) {
                if (!preg_match('#(?<title>.*?)?( - )?((?<genres>[^-]+))?$#u', $titleAndGenres, $titleAndGenresMatch)) {
                    throw new CM_Exception_Invalid('Cannot parse titleAndGenres.', null, ['string' => $titleAndGenres]);
                }
                if (!empty($titleAndGenresMatch['title'])) {
                    $title = $titleAndGenresMatch['title'];
                }
                if (!empty($titleAndGenresMatch['genres'])) {
                    $genres = new Denkmal_Scraper_Genres($titleAndGenresMatch['genres']);
                }
            }

            $textTitle = $event->find('.summary .title')->getText();

            $description = new Denkmal_Scraper_Description($textTitle, $title, $genres);

            $from = new Denkmal_Scraper_Date($day, $month, null, $now);
            if ($from->getWeekday() == 6) {
                $from->setTime(23); // Sa
            } else {
                $from->setTime(20);
            }

            return new Denkmal_Scraper_EventData($this->getRegion(), $venueName, $description, $from);
        });
    }

    public function getRegion() {
        return Denkmal_Model_Region::getBySlug('basel');
    }
}
