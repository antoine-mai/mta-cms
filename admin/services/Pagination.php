<?php namespace Admin\Services;

use Admin\Core\Registry;
use Admin\Core\Error;

/**
 * Pagination Class
 * 
 * Handles generation of pagination links.
 */
class Pagination
{
	protected $baseUrl		= '';
	protected $prefix = '';
	protected $suffix = '';
	protected $total_rows = 0;
	protected $num_links = 2;
	public $per_page = 10;
	public $cur_page = 0;
	protected $use_page_numbers = false;
	protected $first_link = '&lsaquo; First';
	protected $next_link = '&gt;';
	protected $prev_link = '&lt;';
	protected $last_link = 'Last &rsaquo;';
	protected $uri_segment = 0;
	protected $full_tag_open = '';
	protected $full_tag_close = '';
	protected $first_tag_open = '';
	protected $first_tag_close = '';
	protected $last_tag_open = '';
	protected $last_tag_close = '';
	protected $first_url = '';
	protected $cur_tag_open = '<strong>';
	protected $cur_tag_close = '</strong>';
	protected $next_tag_open = '';
	protected $next_tag_close = '';
	protected $prev_tag_open = '';
	protected $prev_tag_close = '';
	protected $num_tag_open = '';
	protected $num_tag_close = '';
	protected $page_query_string = false;
	protected $query_string_segment = 'per_page';
	protected $display_pages = true;
	protected $_attributes = '';
	protected $_link_types = [];
	protected $reuse_query_string = false;
	protected $use_global_url_suffix = false;
	protected $data_page_attr = 'data-ci-pagination-page';

	public function __construct($params = [])
	{
		$loader = Registry::getInstance('Loader');
		$lang = Registry::getInstance('Language');

		$loader->language('pagination');
		foreach (['first_link', 'next_link', 'prev_link', 'last_link'] as $key)
		{
			if (($val = $lang->line('pagination_'.$key)) !== false)
			{
				$this->$key = $val;
			}
		}
		isset($params['attributes']) or $params['attributes'] = [];
		$this->initialize($params);
		Error::logMessage('info', 'Pagination Class Initialized');
	}

	public function initialize(array $params = [])
	{
		if (isset($params['attributes']) && is_array($params['attributes']))
		{
			$this->_parse_attributes($params['attributes']);
			unset($params['attributes']);
		}
		if (isset($params['anchor_class']))
		{
			empty($params['anchor_class']) or $attributes['class'] = $params['anchor_class'];
			unset($params['anchor_class']);
		}
		foreach ($params as $key => $val)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $val;
			}
		}

		$config = Registry::getInstance('Config');
		if ($config->item('enable_query_strings') === true)
		{
			$this->page_query_string = true;
		}
		if ($this->use_global_url_suffix === true)
		{
			$this->suffix = $config->item('url_suffix');
		}
		return $this;
	}

	public function create_links()
	{
		if ($this->total_rows == 0 or $this->per_page == 0)
		{
			return '';
		}
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($num_pages === 1)
		{
			return '';
		}
		$this->num_links = (int) $this->num_links;
		if ($this->num_links < 0)
		{
			Error::showError('Your number of links must be a non-negative number.');
		}

		$request = Registry::getInstance('Request');

		if ($this->reuse_query_string === true)
		{
			$get = $request->query->all();
			unset($get['c'], $get['m'], $get[(string)$this->query_string_segment]);
		}
		else
		{
			$get = [];
		}
		$baseUrl = trim((string)$this->baseUrl);
		$first_url = (string)$this->first_url;
		$query_string_sep = (strpos($baseUrl, '?') === false) ? '?' : '&amp;';
		if ($this->page_query_string === true)
		{
			if ($first_url === '')
			{
				$first_url = $baseUrl;
				if ( ! empty($get))
				{
					$first_url .= $query_string_sep.http_build_query($get);
				}
			}
			$baseUrl .= $query_string_sep.http_build_query(array_merge($get, [(string)$this->query_string_segment => '']));
		}
		else
		{
			if ( ! empty($get))
			{
				$this->suffix .= $query_string_sep.http_build_query($get);
			}
			if ($this->reuse_query_string === true && ($base_query_pos = strpos($baseUrl, '?')) !== false)
			{
				$baseUrl = substr($baseUrl, 0, $base_query_pos);
			}
			if ($first_url === '')
			{
				$first_url = $baseUrl.(($this->suffix !== '' && strpos((string)$this->suffix, $query_string_sep) !== 0) ? $query_string_sep.$this->suffix : $this->suffix);
			}
			$baseUrl = rtrim($baseUrl, '/').'/';
		}
		$base_page = ($this->use_page_numbers) ? 1 : 0;
		if ($this->page_query_string === true)
		{
			$this->cur_page = $request->get((string)$this->query_string_segment);
		}
		elseif (empty($this->cur_page))
		{
			$uri = Registry::getInstance('Uri');
			if ($this->uri_segment == 0)
			{
				$this->uri_segment = count($uri->segmentArray());
			}
			$this->cur_page = $uri->segment((int)$this->uri_segment);
			if ($this->prefix !== '' or $this->suffix !== '')
			{
				$this->cur_page = str_replace([$this->prefix, $this->suffix], '', (string)$this->cur_page);
			}
		}
		else
		{
			$this->cur_page = (string) $this->cur_page;
		}
		if ( ! ctype_digit((string)$this->cur_page) or ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			$this->cur_page = (int) $this->cur_page;
		}
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}
		$uri_page_number = $this->cur_page;
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page/$this->per_page) + 1);
		}
		$start	= (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end	= (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
		$output = '';
		if ($this->first_link !== false && $this->cur_page > ($this->num_links + 1 + ! $this->num_links))
		{
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, 1);
			$output .= $this->first_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
				.$this->first_link.'</a>'.$this->first_tag_close;
		}
		if ($this->prev_link !== false && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? (int)$uri_page_number - 1 : (int)$uri_page_number - (int)$this->per_page;
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, ($this->cur_page - 1));
			if ($i === $base_page)
			{
				$output .= $this->prev_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$append = (string)$this->prefix.$i.(string)$this->suffix;
				$output .= $this->prev_tag_open.'<a href="'.$baseUrl.$append.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
		}
		if ($this->display_pages !== false)
		{
			for ($loop = $start - 1; $loop <= $end; $loop++)
			{
				$i = ($this->use_page_numbers) ? $loop : ($loop * $this->per_page) - $this->per_page;
				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $loop);
				if ($i >= $base_page)
				{
					if ($this->cur_page === $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
					}
					elseif ($i === $base_page)
					{
						$output .= $this->num_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
					else
					{
						$append = (string)$this->prefix.$i.(string)$this->suffix;
						$output .= $this->num_tag_open.'<a href="'.$baseUrl.$append.'"'.$attributes.'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
				}
			}
		}
		if ($this->next_link !== false && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $this->cur_page + 1);
			$output .= $this->next_tag_open.'<a href="'.$baseUrl.(string)$this->prefix.$i.(string)$this->suffix.'"'.$attributes
				.$this->_attr_rel('next').'>'.$this->next_link.'</a>'.$this->next_tag_close;
		}
		if ($this->last_link !== false && ($this->cur_page + $this->num_links + ! $this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $num_pages);
			$output .= $this->last_tag_open.'<a href="'.$baseUrl.(string)$this->prefix.$i.(string)$this->suffix.'"'.$attributes.'>'
				.$this->last_link.'</a>'.$this->last_tag_close;
		}
		$output = preg_replace('#([^:"])//+#', '\\1/', (string)$output);
		return $this->full_tag_open.$output.$this->full_tag_close;
	}

	protected function _parse_attributes($attributes)
	{
		isset($attributes['rel']) or $attributes['rel'] = true;
		$this->_link_types = ($attributes['rel'])
			? ['start' => 'start', 'prev' => 'prev', 'next' => 'next']
			: [];
		unset($attributes['rel']);
		$this->_attributes = '';
		foreach ($attributes as $key => $value)
		{
			$this->_attributes .= ' '.$key.'="'.$value.'"';
		}
	}

	protected function _attr_rel($type)
	{
		if (isset($this->_link_types[$type]))
		{
			unset($this->_link_types[$type]);
			return ' rel="'.$type.'"';
		}
		return '';
	}
}
