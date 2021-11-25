<?
class Minio {
	private $s3;
	private $bucket;
	private $image_url;
	function __construct($Endpoint = MINIO_ENDPOINT, $Key = MINIO_KEY, $Secret = MINIO_SECRET, $Bucket = MINIO_BUCKET, $ImageUrl = IMAGE_URL) {
		$this->s3 = new Aws\S3\S3Client([
			'version' => 'latest',
			'endpoint' => $Endpoint,
			'region'  => 'us-east-1',
			'use_path_style_endpoint' => true,
			'credentials' => [
				'key'    => $Key,
				'secret' => $Secret,
			],
		]);
		$this->bucket = $Bucket;
		$this->image_url = $ImageUrl;
	}
	public function get_user_image_presigned_url($user_id, $name) {
		$name_strs = explode('.', $name);
		$type = $name_strs[count($name_strs) - 1];
		$new_key = 'user/' . $user_id . '/' . date('Ymd', time()) . '/' . uniqid() . '.' . $type;

		$command = $this->s3->getCommand('PutObject', [
			'Bucket' => $this->bucket,
			'Key'    => $new_key,
		]);

		// Create a pre-signed URL for a request with duration of 10 miniutes
		$presignedRequest = $this->s3->createPresignedRequest($command, '+3 minutes');

		// Get the actual presigned-url
		$presignedUrl =  (string)  $presignedRequest->getUri();
		return ['url' => $presignedUrl, 'name' => $this->image_path($new_key)];
	}
	private function image_path($key) {
		$bucket = $this->bucket;
		return  $this->image_url . "/$bucket/$key";
	}
	public function upload($Key, $Body) {
		$file_info = new finfo(FILEINFO_MIME_TYPE);
		$mime_type = $file_info->buffer($Body);
		$this->s3->putObject([
			'Bucket' => $this->bucket,
			'Key'    => $Key,
			'Body'   => $Body,
			'ContentType' => $mime_type,
		]);
		return $this->image_path($Key);
	}
	private function file_get_contents($Url) {
		if (!function_exists('curl_init')) {
			die('CURL is not installed!');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	public function fetch_upload($Key, $Url) {
		try {
			$data = $this->file_get_contents($Url);
		} catch (Exception $e) {
			error_log($e);
			return "";
		}
		return $this->upload($Key, $data);
	}
}
