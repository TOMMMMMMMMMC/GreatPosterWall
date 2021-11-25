<?
echo json_encode($Minio->get_user_image_presigned_url($LoggedUser['ID'], $_GET['name']));
