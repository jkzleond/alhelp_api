<?php
namespace Common\lib;

/**
 * Description of smsto
 * 手机短信发送类
 * @author Auser
 */
class smsto
{
	protected $user;
	protected $password;
	protected $url;
	protected $ch;
	protected $error;
	public function __construct($user, $password, $url)
	{
		$mobile = C ( 'MOBILE_CONFIG' );
		$this->user = empty ( $user ) ? $mobile ['USER'] : $user;
		$this->password = empty ( $password ) ? $mobile ['PASSWORD'] : $password;
		$this->url = empty ( $url ) ? $mobile ['URL'] : $url;
		$this->ch = curl_init ();
	}
	
	protected function createXmlData_Mobiles($mobilephone)
	{
		$xml = '';
		foreach ( $mobilephone as $v )
		{
			$xml .= <<<EOT
				  <Mobile>
					 <Number>$v</Number>
					 <Operator>0</Operator>
					 <ServiceNumber>0</ServiceNumber>			
					 <SendNumber>0</SendNumber>
				  </Mobile>
EOT;
		}
		
		return $xml;
	
	}
	
	// ���ÿһ�����ݲ���ÿ����Ϣ��XML
	protected function createXmlData_Messages($mobilephone, $content)
	{
		$content = iconv ( "utf-8", "gb2312//IGNORE", $content );
		//global $msgids;  // ����ID  �������У�����ȥ��ʱ��ʹ��
		

		$mobiles = $this->createXmlData_Mobiles ( $mobilephone ); // ��ȡ�ֻ����
		

		$xml = '';
		
		$Msg_Id = time () . rand ( 1000, 999999 ); // ��ϢID �� ����Ψһ , �� ID �� ÿһ���ֻ���� ���Ψһ��ʶ�� �����ڷ��ͻ�ִ����
		

		array_push ( $msgids, $Msg_Id );
		
		$Submit_Time = date ( 'Y-m-d H:i:s', time () );
		
		// print  "��ǰʱ��:" . $Submit_Time . "<br />" ;	
		

		$content = base64_encode ( $content );
		
		$xml .= <<<EOT
			<Message>
						
			   	   <Msg_Type>1</Msg_Type>
						
				   <Msg_Id>$Msg_Id</Msg_Id>
						
				   <Organ_Id>0</Organ_Id>
						
				   <User_Id>0</User_Id>
						
				   <Submit_Time>$Submit_Time</Submit_Time>
						
				   <Channel_Code>0</Channel_Code>
						
				   <Msg_Content>$content</Msg_Content>
						
				   <Priority>2</Priority>
						
				   <Mobiles>
						$mobiles;
					</Mobiles>
			</Message>		
EOT;
		
		return $xml;
	
	}
	
	// ����ֻ����(����)���������飬����XML
	protected function createXmlData($mobilephone, $content)
	{
		$Customer_ID = $this->user;
		$Password = $this->password;
		$Package_Seq = time () . rand ( 1000, 999999 ); // ������ �� ������Ψһ��
		

		$xml = "<Messages>";
		
		foreach ( $content as $v )
		{
			$message = $this->createXmlData_Messages ( $mobilephone, $v ); // ÿ����Ϣ
			$xml .= <<<EOT
				 $message
EOT;
		}
		
		$xml .= "</Messages>";
		
		// ����XML
		return <<< EOT
<?xml version="1.0" encoding="gbk" ?>				
			<ShortMessages>
					
					 <Customer_Id>$Customer_ID</Customer_Id>
					
                     <Mac_Addr>$Password</Mac_Addr>
					
					 <Package_Seq>{$Package_Seq}</Package_Seq>
					
					 <Version>2.0</Version>
					
					 $xml				
	
      	   </ShortMessages>
EOT;
	}
	
	// 执行发送短信���͵�������
	public function SendToServer($mobilephone, $content)
	{
		$ch = $this->ch;
		$xmldata = $this->createXmlData ( $mobilephone, $content );
		
		//	Global $ch ;  // ʹ���ⲿ����
		$Customer_ID = $this->user;
		
		$rndnum = rand ( 1, 9999999 ); // Ϊ�˱��⻺�棬���� �����
		

		// ���� URL ��ַ ��Customer_Id �� �ͻ�ID = �۰ĵĿͻ�ID ���ͻ����������ļ��ж�ȡ. �� 102733
		

		$url = $this->url . "?rmd=" . $rndnum . "&id=" . $Customer_ID;
		
		$header [] = "Content-type: text/xml;charset=gbk"; // ����content-typeΪxml
		

		// ���м����Եı���
		$data = urlencode ( urlencode ( $xmldata ) );
		
		// HTTP ͷ
		$header [] = "Content-length: " . strlen ( $data );
		
		// ���� URL
		curl_setopt ( $ch, CURLOPT_URL, $url );
		
		// ���� ���ɹ�ֻ�����أ����Զ�����κ����ݡ����ʧ�ܷ���FALSE
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		
		// ���� HTTP ͷ
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		
		curl_setopt ( $ch, CURLOPT_ENCODING, '' );
		
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); // ���÷������
		

		// ����д���Ļ������´��?���û�оͲ�����
		//    $proxy="128.1.241.11:80" ;
		curl_setopt ( $ch, CURLOPT_PROXY, $proxy );
		
		$response = curl_exec ( $ch ); // ִ�з��͹��     
		curl_close ( $ch );
		return $this->analytical ( $response );
	}
	
	public function analytical($xml)
	{
		$xml = simplexml_load_string ( $xml );
		if ($xml->Error_Code == 0)
		{ //发送成功
			return true;
		} else if ($xml->Error_Code == '-1')
		{
			$this->error = '发送失败';
			return false;
		} else if ($xml->Error_Code == '-2')
		{
			$this->error = '没有该用户';
			return false;
		} else if ($xml->Error_Code == '100')
		{
			$this->error = '没有发送数据';
			return false;
		} else if ($xml->Error_Code == '-3')
		{
			$this->error = '没有网卡号';
			return false;
		} else if ($xml->Error_Code == '-4')
		{
			$this->error = '网卡号和用户不匹配';
			return false;
		} else if ($xml->Error_Code == '-5')
		{
			$this->error = '没有信息内容';
			return false;
		} else if ($xml->Error_Code == '-6')
		{
			$this->error = '没有交验序号';
			return false;
		} else if ($xml->Error_Code == '-7')
		{
			$this->error = '序号重复';
			return false;
		} else if ($xml->Error_Code == '-8')
		{
			$this->error = '包序号重复';
			return false;
		} else if ($xml->Error_Code == '-9')
		{
			$this->error = '文件输出错误';
			return false;
		} else if ($xml->Error_Code == '-10')
		{
			$this->error = '时间错误';
			return false;
		} else if ($xml->Error_Code == '-11')
		{
			$this->error = '随机数空';
			return false;
		} else if ($xml->Error_Code == '-12')
		{
			$this->error = '数据长度错误';
			return false;
		} else if ($xml->Error_Code == '-15')
		{
			$this->error = '数据包不完整 建议重发';
			return false;
		} else if ($xml->Error_Code == '-16')
		{
			$this->error = '用户id号太长';
			return false;
		} else if ($xml->Error_Code == '-17')
		{
			$this->error = '用户密码太长';
			return false;
		} else if ($xml->Error_Code == '-18')
		{
			$this->error = '编码解码错误';
			return false;
		} else
		{
			$this->error = '其它错误';
			return false;
		}
	}
	
	public function get_error()
	{
		return $this->error;
	}

}
