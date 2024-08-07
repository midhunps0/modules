<?php
namespace Modules\Ynotz\Metatags\Helpers;

use Countable;
use Illuminate\Support\Facades\Session;

class MetatagHelper
{
    /**
     * Clear metatags in the session
     *
     * @return void
     */
    public static function clearMetatags(): void
    {
        session()->put('metatags', []);
    }

    /**
     * Clear title in the session
     *
     * @return void
     */
    public static function clearTitle(): void
    {
        session()->remove('title');
    }

    /**
     * Clear both meta tags & title in the session
     *
     * @return void
     */
    public static function clearAllMeta(): void
    {
        session()->put('metatags', []);
        session()->remove('title');
    }

    /**
     * get all metatags in the session as json encoded string
     *
     * @return string
     */
    public static function getMetatags()
    {
        return is_array(session('metatags')) && count(session('metatags')) > 0 ? json_encode(session('metatags')) : null;
    }

    /**
     * set the paeg title (html title tag content)
     *
     * @param string $title
     * @return void
     */
    public static function setTitle(string $title)
    {
        session()->put('title', $title);
    }

    /**
     * get the page title (html title tag content)
     *
     * @return void
     */
    public static function getTitle()
    {
        return session('title');
    }

    /**
     * add meta tag
     *
     * @param [type] $name
     * @param [type] $content
     * @return void
     */
    public static function addTag($name, $content)
    {
        $metatags = session('metatags', []);
        $metatags[] = ['name' => $name, 'content' => $content];
        session()->put('metatags', $metatags);
    }

    /**
     * Add metatag by properties and their values
     *
     * @param array $props
     * @return void
     */
    public static function addTagByProps(array $props)
    {
        $metatags = session('metatags', []);
        $temp = [];
        foreach ($props as $name => $content) {
            $temp[$name] = $content;
        }
        $temp['by_props'] = true;
        $metatags[] = $temp;
        session()->put('metatags', $metatags);
    }

    /**
     * add og meta tag
     *
     * @param string $property
     * @param string $content
     * @return void
     */
    public static function addOgTag($property, $content)
    {
        $metatags = session('metatags', []);
        $metatags[] = ['property' => 'og:'.$property, 'content' => $content];
        session()->put('metatags', $metatags);
    }

    /**
     * adds Metatags
     *
     * @param array $tags associative array of required tags ['name' => 'content']
     * @return void
     */
    public static function addMetatags(array $tags): void
    {
        foreach ($tags as $t => $v) {
            Self::addTag($t, $v);
        }
    }

    /**
     * add Og Metatags
     *
     * @param array $tags associative array of required tags ['name' => 'content']
     * @return void
     */
    public static function addOgTags(array $tags): void
    {
        foreach ($tags as $t => $v) {
            Self::addOgTag($t, $v);
        }
    }
}
?>
