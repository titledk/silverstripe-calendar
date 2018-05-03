<?php

namespace TitleDK\Calendar\Tags;

use SilverStripe\ORM\DataObject;
use TitleDK\Calendar\Events\Event;

/**
 * A blog tag for keyword descriptions of a blog post.
 *
 *
 * @method Blog Blog()
 *
 * @property string $Title
 * @property string $URLSegment
 * @property int $BlogID
 */
class EventTag extends DataObject
{
    //use BlogObject;

    /**
     * Use an exception code so that attempted writes can continue on
     * duplicate errors.
     *
     * @const string
     * This must be a string because ValidationException has decided we can't use int
     */
    const DUPLICATE_EXCEPTION = 'DUPLICATE';

    /**
     * {@inheritDoc}
     * @var string
     */
    private static $table_name = 'EventTag';

    /**
     * @var array
     */
    private static $db = [
        'Title'      => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)'
    ];

    /**
     * @var array
    // todo make tags per calendar
    private static $has_one = [
        'Calendar' => Calendar::class
    ];
*/

    /**
     * @var array
     */
    private static $many_many = [
        'Events' => Event::class
    ];

    /**
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t(__CLASS__ . '.Duplicate', 'A blog tag already exists with that name.');
    }
}
