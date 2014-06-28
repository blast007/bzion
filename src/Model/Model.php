<?php
/**
 * This file contains the skeleton for all of the database objects
 *
 * @package    BZiON\Models
 * @license    https://github.com/allejo/bzion/blob/master/LICENSE.md GNU General Public License Version 3
 */

use \Michelf\Markdown;

/**
 * A database object (e.g. A player or a team)
 * @package    BZiON\Models
 */
abstract class Model extends CachedModel
{
    /**
     * Generates a string with the object's type and ID
     */
    public function __toString()
    {
        return get_class($this) . " #" . $this->getId();
    }

    /**
     * Find if the model is in the trash can (or doesn't exist)
     *
     * @return boolean True if the model has been deleted
     */
    public function isDeleted()
    {
        if (!$this->isValid())
            return true;

        if (!isset($this->status))
            return false;

        return ($this->status == "deleted");
    }

    /**
     * Converts an array of IDs to an array of Models
     * @param  int[] $idArray The list of IDs
     * @return array An array of models
     */
    public static function arrayIdToModel($idArray)
    {
        $return = array();
        foreach ($idArray as $id) {
            $return[] = new static($id);
        }

        return $return;
    }

    /**
     * Update a property and the corresponding database column
     *
     * @param  mixed  $property The protected class property to update
     * @param  string $dbColumn The name of the database column to update
     * @param  mixed  $value    The value to insert
     * @param  string $type     The mysqli type of the value (s, i, d, b)
     * @return self   Returns the model itself to allow method chaining
     */
    protected function updateProperty(&$property, $dbColumn, $value, $type = 'i')
    {
        // Don't waste time with mysql if there aren't any changes
        if ($property != $value) {
            $property = $value;

            if ($value instanceof TimeDate) {
                $value = $value->toMysql();
            }

            $this->update($dbColumn, $value, $type);
        }

        return $this;
    }

    /**
     * Gets the type of the model
     * @return string The type of the model, e.g. "server"
     */
    public static function getType()
    {
        return self::toSnakeCase(get_called_class());
    }

    /**
     * Gets a human-readable format of the model's type
     * @return string
     */
    public static function getTypeForHumans()
    {
        return self::getType();
    }

    /**
     * Takes a CamelCase string and converts it to a snake_case one
     * @param $input The string to convert
     * @return string
     */
    private static function toSnakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Convert a markdown string to HTML. This function is used to have one global configuration with all markdown parsing
     *
     * @param string $text The markdown to be parsed to HTML
     *
     * @return string Return the parsed markdown
     */
    public static function mdTransform($text)
    {
        $mdParser = new Markdown();
        $mdParser->no_entities = true;

        return $mdParser->transform($text);
    }

    /**
     * Escape special HTML characters from a string
     * @param  string  $string
     * @return $string
     */
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}