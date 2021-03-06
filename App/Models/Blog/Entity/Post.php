<?php
namespace App\Models\Blog\Entity;

class Post
{
	private $id;
	private $creationDate;
	private $updateDate;
	private $title;
	private $intro; // chapô
	private $content;
	private $slug;
	private $userId;

	// Temporary params which are not in database but useful in methods
	private $temporaryParams = [];

	public function __construct(array $array)   
	{
		$this->hydrate($array);
	}

	public function hydrate($datas)
	{
		$classShortName = (new \ReflectionClass($this))->getShortName();
		$classPrefix = strtolower($classShortName) . '_';
		foreach ($datas as $key => $value) {
			// Define setter: replace "classname_" tables fields prefix syntax by nothing
		    $method = 'set' . ucfirst(str_replace($classPrefix, '', $key)); 
		    
		    // Does setter exist?
		    if (method_exists($this, $method)) {
		      	// Call setter
		      	$this->$method($value);
		    }
		    else {
		    	// Call magic __set
		    	$this->$key = $value;
		    }
		}
	 
	}

	public function __set($name, $value)
	{	    
	    if(method_exists($this, $name)) {
	      	$this->$name($value);
	    }
	    else{
	      	// Setter is not defined so set as property of object
	      	$key = lcfirst(str_replace('set', '', $name)); 
	      	$this->temporaryParams[$key] = $value;
	    }
  	}

	public function __get($name)
	{
	    if(method_exists($this, $name)) {
	      return $this->$name();
	    }
	    elseif(property_exists($this, $name)){
	      // Getter is not defined so return property if it exists
	      return $this->$name;
	    }
	    elseif(array_key_exists($name, $this->temporaryParams)) {
	    	return $this->temporaryParams[$name];
	    }
	    else {
	    	return null;
	    }  
	}

	public function getTemporaryParams() 
	{
		return $this->temporaryParams;
	}

	// Getters
	
	public function getId() 
	{
		return $this->id;
	}

	public function getCreationDate()
	{
		return $this->creationDate;
	}

	public function getUpdateDate()
	{
		return $this->updateDate;
	}

	public function getTitle() 
	{
		return $this->title;
	}

	public function getIntro()
	{
		return $this->intro;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getSlug()
	{
		return $this->slug;
	}

	public function getUserId() // get author id
	{
		return $this->userId;
	}

	// Setters
	
	public function setId($id) 
	{
		$id = (int) $id;
		if ($id > 0) {
	    	$this->id = $id;
	    }
	}

	public function setCreationDate($creationDate)
	{
		if(is_string($creationDate)) {
			$date = new \DateTime($creationDate);
	      	$this->creationDate = date_format($date, 'd-m-Y H:i:s');
	    }
	}

	public function setUpdateDate($updateDate)
	{
		if(is_string($updateDate)) {
			$date = new \DateTime($updateDate);
	      	$this->updateDate = date_format($date, 'd-m-Y H:i:s');
		}
	}

	public function setTitle($title) 
	{
		if(is_string($title)) {
	      	$this->title = $title;
	    }
	}

	public function setIntro($intro)
	{
		if (is_string($intro)) {
	      	$this->intro = $intro;
	    }
	}

	public function setContent($content)
	{
		if (is_string($content)) {
	      	$this->content = $content;
	    }
	}

	public function setSlug($slug) 
	{
		if (is_string($slug)) {
	      	$this->slug = $slug;
	    }
	}

	public function setUserId($userId)  // set author id
	{
		$userId = (int) $userId;
		if ($userId > 0) {
	      	$this->userId = $userId;
	    }
	}
}