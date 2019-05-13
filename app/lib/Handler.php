<?

class Handler 
{
	private $name;
	private $phone;
	private $email;
	private $comments;

	private $country;
	private $currentDate;
	private $utmTags;
	private $msgContent;

	function __construct()
	{
		$this->currentDate = date("d-F-Y, D, H:i");
		$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$_REQUEST['REMOTE_ADDR']));
		if($query['status'] == 'success'){
			$this->country = $query['country'];
		} else {
			$this->country = "Не получилось определить страну";
		}
	}

	private function setName($name)
	{
		if(trim(strip_tags($name))  == trim($name) || $name)
		{
			$this->name = trim(strip_tags($name));
			return true;
		}
		return false;
	}

	private function setPhone($phone)
	{
		if(trim(strip_tags($phone)) == trim($phone) || $phone)
		{
			$this->phone = trim(strip_tags($phone));
			return true;
		}
		return false;
	}

	private function setEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->email = trim($email);
			return true;
		}
		return false;
	}

	private function setComments($comments)
	{
		if(trim(strip_tags($comments)) == trim($comments) || $comments)
		{
			$this->comments = trim(strip_tags($comments));
			return true;
		}
		return false;
	}

	public function setUtmTags($utm_query)
	{
		if($utm_query){
			$this->utmTags = explode("&", $utm_query);
			return true;
		}
		return false;
	}

	public function setFields($post)
	{
		if(!$this->setName($post['name']))
		{
			return ERR_BAD_NAME;
		}

		if(!$this->setPhone($post['phone']))
		{
			return ERR_BAD_PHONE;
		}

		if(!$this->setEmail($post['email']))
		{
			return ERR_BAD_EMAIL;
		}

		if(!$this->setComments($post['Comments']))
		{
			return ERR_BAD_COMMENTS;
		}
		return true;
	}

	public function setMsgContent()
	{
		$this->msgContent = "
			<h2 align='center' style='color: #333333; font: Arial, sans-serif; line-height: 30px; -webkit-text-size-adjust:none;'>Заявка с сайта " . SITE_NAME . "!</h2><br /><hr>
			<h3 align='center' style='color: #333333; font: Arial, sans-serif; line-height: 30px; -webkit-text-size-adjust:none;'>Данные клиента:</h3>
			<p style='color: #333333; font: Arial, sans-serif; -webkit-text-size-adjust:none;'>Заявка отравлена: $this->currentDate;<br />
			Страна: $this->country<br />
			Имя: $this->name<br />
			E-mail: $this->email<br />
			Телефон: $this->phone<br />
			Комменты: $this->comments<br /></p>
			";

		if ($this->utmTags) {
			$this->msgContent .= "
				<hr><h3 align='center' style='color: #333333; font: Arial, sans-serif; line-height: 30px; -webkit-text-size-adjust:none;'>Данные по UTM-метке</h3><br />
				{$this->utmTags[0]}<br />
				{$this->utmTags[1]}<br />
				{$this->utmTags[2]}<br />
			";
		}

		if ($this->utmTags[3]) {
			$this->msgContent .= "{$this->utmTags[3]}<br />";
		}

		if ($this->utmTags[4]) {
			$msgContent .= "{$this->utmTags[4]}<br />";
		}
		return $this->msgContent;
	}

	public function sendMail()
	{
		if($send = mail(EMAIL_WHERE, SUBJECT, $this->msgContent, "Content-type:text/html; charset = utf-8\r\nFrom:" . EMAIL_FROM))
		{
			return OK_SENDED;
		}
		return ERR_NOT_SENDED;
	}

}