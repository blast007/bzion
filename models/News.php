<?php
/**
 * This file contains functionality relating to the news articles admins can post
 *
 * @package    BZiON\Models
 * @license    https://github.com/allejo/bzion/blob/master/LICENSE.md GNU General Public License Version 3
 */

/**
 * A news article
 * @package    BZiON\Models
 */
class News extends UrlModel implements NamedModel
{
    /**
     * The category of the article
     * @var int
     */
    protected $category;

    /**
     * The subject of the news article
     * @var string
     */
    protected $subject;

    /**
     * The content of the news article
     * @var string
     */
    protected $content;

    /**
     * The creation date of the news article
     * @var TimeDate
     */
    protected $created;

    /**
     * The date the news article was last updated
     * @var TimeDate
     */
    protected $updated;

    /**
     * The ID of the author of the news article
     * @var int
     */
    protected $author;

    /**
     * The ID of the last person to edit the news article
     * @var int
     */
    protected $editor;

    /**
     * The status of the news article
     * @var string
     */
    protected $status;

    /**
     * The name of the database table used for queries
     */
    const TABLE = "news";

    const CREATE_PERMISSION = Permission::CREATE_NEWS;
    const EDIT_PERMISSION = Permission::EDIT_NEWS;
    const SOFT_DELETE_PERMISSION = Permission::SOFT_DELETE_NEWS;
    const HARD_DELETE_PERMISSION = Permission::HARD_DELETE_NEWS;

    /**
     * {@inheritdoc}
     */
    protected function assignResult($news)
    {
        $this->category = $news['category'];
        $this->subject = $news['subject'];
        $this->content = $news['content'];
        $this->created = TimeDate::fromMysql($news['created']);
        $this->updated = TimeDate::fromMysql($news['updated']);
        $this->author = $news['author'];
        $this->editor = $news['editor'];
        $this->status = $news['status'];
    }

    /**
     * Get the author of the news article
     * @return Player The author of the post
     */
    public function getAuthor()
    {
        return Player::get($this->author);
    }

    /**
     * Get the user ID of the author who wrote this article
     * @return int The author ID
     */
    public function getAuthorID()
    {
        return $this->author;
    }

    /**
     * Get the category of the news article
     * @return NewsCategory The category of the post
     */
    public function getCategory()
    {
        return NewsCategory::get($this->category);
    }

    /**
     * Get the database ID from the category the article belongs into
     * @return int The category ID
     */
    public function getCategoryID()
    {
        return $this->category;
    }

    /**
     * Get the content of the article
     * @return string The raw content of the article
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the time when the article was submitted
     *
     * @return TimeDate The article's creation time
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get the time when the article was last updated
     *
     * @return TimeDate The article's last update time
     */
    public function getLastEdit()
    {
        return $this->updated;
    }

    /**
     * Get the last editor of the post
     * @return Player A Player object of the last editor
     */
    public function getLastEditor()
    {
        return Player::get($this->editor);
    }

    /**
     * Get the ID of the person who last edited the article
     * @return int The ID of the last editor
     */
    public function getLastEditorID()
    {
        return $this->editor;
    }

    /**
     * Get the subject of the news article
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public static function getParamName()
    {
        return "article";
    }

    /**
     * {@inheritdoc}
     */
    public static function getRouteName($action = 'show')
    {
        return "news_$action";
    }

    /**
     * Update the content of a post
     *
     * @param string $content The new content of the post
     *
     * @return self
     */
    public function updateContent($content)
    {
        return $this->updateProperty($this->content, 'content', $content, 's');
    }

    /**
     * Update the last edit timestamp
     * @return self
     */
    public function updateEditTimestamp()
    {
        return $this->updateProperty($this->updated, 'updated', TimeDate::now(), 's');
    }

    /**
     * Update the editor of the post
     *
     * @param  int  $editorID The ID of the editor
     * @return self
     */
    public function updateLastEditor($editorID)
    {
        return $this->updateProperty($this->editor, 'editor', $editorID);
    }

    /**
     * Update the category of the post
     *
     * @param  int  $categoryID The ID of the category
     * @return self
     */
    public function updateCategory($categoryID)
    {
        return $this->updateProperty($this->category, 'category', $categoryID);
    }

    /**
     * Update the status of a post
     *
     * @param  string $status The new status of a post
     * @return self
     */
    public function updateStatus($status = 'published')
    {
        return $this->updateProperty($this->status, 'status', $status, 's');
    }

    /**
     * Update the subject of a post
     *
     * @param  string $subject The new subject of a post
     * @return self
     */
    public function updateSubject($subject)
    {
        return $this->updateProperty($this->subject, 'subject', $subject, 's');
    }

    /**
     * {@inheritdoc}
     */
    public static function getActiveStatuses()
    {
        return array('published', 'revision');
    }

    /**
     * Add a new news article
     *
     * @param string $subject    The subject of the article
     * @param string $content    The content of the article
     * @param int    $authorID   The ID of the author
     * @param int    $categoryId The ID of the category this article will be published under
     * @param string $status     The status of the article: 'published', 'disabled', or 'deleted'
     *
     * @internal param int $categoryID The ID of the category
     * @return News An object representing the article that was just created or false if the article was not created
     */
    public static function addNews($subject, $content, $authorID, $categoryId = 1, $status = 'published')
    {
        return self::create(array(
            'category' => $categoryId,
            'subject'  => $subject,
            'content'  => $content,
            'author'   => $authorID,
            'editor'   => $authorID,
            'status'   => $status,
        ), 'issiis', array('created', 'updated'));
    }

    /**
     * Get all the news entries in the database that aren't disabled or deleted
     *
     * @param int  $start     The offset used when fetching matches, i.e. the starting point
     * @param int  $limit     The amount of matches to be retrieved
     * @param bool $getDrafts Whether or not to fetch drafts
     *
     * @return News[] An array of news objects
     */
    public static function getNews($start = 0, $limit = 5, $getDrafts = false)
    {
        $ignoredStatuses[] = "deleted";

        if (!$getDrafts) {
            $ignoredStatuses[] = "draft";
        }

        return self::arrayIdToModel(
            self::fetchIdsFrom(
                "status", $ignoredStatuses, "s", true,
                "ORDER BY created DESC LIMIT $limit OFFSET $start"
            )
        );
    }

    /**
     * Get a query builder for news
     * @return QueryBuilder
     */
    public static function getQueryBuilder()
    {
        return new QueryBuilder('News', array(
            'columns' => array(
                'subject'  => 'subject',
                'category' => 'category',
                'created'  => 'created',
                'status'   => 'status'
            ),
            'name' => 'subject'
        ));
    }
}
