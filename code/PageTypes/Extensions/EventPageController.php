<?php
namespace TitleDK\Calendar\PageTypes;

class EventPage_Controller extends \PageController
{

    public function ComingOrPastEvents()
    {
        if (isset($_GET['past'])) {
            return 'past';
        } else {
            return 'coming';
        }
    }
    public function Events()
    {
        if ($this->ComingOrPastEvents() == 'past') {
            //return $this->model->PastEvents();
            return $this->PastEvents();
        } else {
            return $this->ComingEvents();
        }
    }
}
