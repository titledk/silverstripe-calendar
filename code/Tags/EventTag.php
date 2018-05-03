<?php

namespace TitleDK\Calendar\Tags;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
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

    // ---- slug related, todo find a module ----

    /**
     * {@inheritdoc}
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->exists() || empty($this->URLSegment)) {
            return $this->generateURLSegment();
        }
    }


    /**
     * Looks for objects o the same type and the same value by the given Field
     *
     * @param  string $field E.g. URLSegment or Title
     * @return DataList
     */
    protected function getDuplicatesByField($field)
    {
        $duplicates = DataList::create(self::class)
            ->filter(
                [
                    $field   => $this->$field,
                  //  'BlogID' => (int) $this->BlogID
                ]
            );

        if ($this->ID) {
            $duplicates = $duplicates->exclude('ID', $this->ID);
        }

        return $duplicates;
    }


    /**
     * Generates a unique URLSegment from the title.
     *
     * @param int $increment
     *
     * @return string
     */
    public function generateURLSegment($increment = 0)
    {
        $increment = (int) $increment;
        $filter = URLSegmentFilter::create();

        // Setting this to on. Because of the UI flow, it would be quite a lot of work
        // to support turning this off. (ie. the add by title flow would not work).
        // If this becomes a problem we can approach it then.
        // @see https://github.com/silverstripe/silverstripe-blog/issues/376
        $filter->setAllowMultibyte(true);

        $this->URLSegment = $filter->filter($this->Title);

        if ($increment > 0) {
            $this->URLSegment .= '-' . $increment;
        }

        if ($this->getDuplicatesByField('URLSegment')->count() > 0) {
            $this->generateURLSegment($increment + 1);
        }

        return $this->URLSegment;
    }
}
