<?php
//index.php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//Variables usadas
$error = '';
$name = '';
$last_name = '';
$email = '';
$password= '';
//No supe para qué era este type
$type=1;

// Función para un texto limpio
function clean_text($string)
{
 $string = trim($string);
 $string = stripslashes($string);
 $string = htmlspecialchars($string);
 return $string;
}
// Código logica form
if(isset($_POST["submit"]))
{
    $message = '';

 if(empty($_POST["name"]))
 {
  $error .= '<p><label class="text-danger">Por favor ingrese el nombre</label></p>';
 }
 else
 {
  $name = clean_text($_POST["name"]);
  if(!preg_match("/^[a-zA-Z ]*$/",$name))
  {
   $error .= '<p><label class="text-danger">Solo letras y espacios es permitido</label></p>';
  }
 }
 if(empty($_POST["last_name"]))
 {
  $error .= '<p><label class="text-danger">Por favor ingrese sus apellidos</label></p>';
 }
 else
 {
  $last_name = clean_text($_POST["last_name"]);
  if(!preg_match("/^[a-zA-Z ]*$/",$last_name))
  {
   $error .= '<p><label class="text-danger">Solo letras y espacios es permitido</label></p>';
  }
 }
 if(empty($_POST["email"]))
 {
  $error .= '<p><label class="text-danger">Please Enter your Email</label></p>';
 }
 else
 {
  $email = clean_text($_POST["email"]);
  if(!filter_var($email, FILTER_VALIDATE_EMAIL))
  {
   $error .= '<p><label class="text-danger">Invalid email format</label></p>';
  }
 }
 if(empty($_POST["password"]))
 {
  $error .= '<p><label class="text-danger">Contraseña requerida</label></p>';
 }
 else
 {
  $password = clean_text($_POST["password"]);
 }
 
 if($error == '')
 {
  $file_open = fopen("contact_data.csv", "a");
  $no_rows = count(file("contact_data.csv"));
  if($no_rows > 1)
  {
   $no_rows = ($no_rows - 1) + 1;
  }
  // datos del form
  $form_data = array(
   'sr_no'  => $no_rows,
   'name'  => $name,
   'last_name'  => $last_name,
   'email'  => $email,
   'password' => $password,
   $type
  );
  //metodo que pone todo en un csv
  fputcsv($file_open, $form_data);

  // De csv a JSON (creo que es equivalente a Excel a JSON)
  
 // $csv=file_get_contents("contact_data.csv");
  //$array= array_map("str_getcsv", explode("\n",$csv));
  //$json= json_encode($array, JSON_PRETTY_PRINT);
 // print_r($json);

// CSV a excel
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
$objPHPExcel = $reader->load('contact_data.csv"');
$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
$objWriter->save('excel_file.xlsx');
// Excel a JSON (Da error si uno tiene el archivo abierto)
$tmpfname = "excel_file.xlsx";
$excelReader = IOFactory::createReaderForFile($tmpfname);
$excelObj = $excelReader->load($tmpfname);
$worksheet = $excelObj->getSheet(0);//
$lastRow = $worksheet->getHighestRow();
$data = [];
for ($row = 1; $row <= $lastRow; $row++) {
     $data[] = [
        //'A' => $worksheet->getCell('A'.$row)->getValue(),
        'name' => $worksheet->getCell('B'.$row)->getValue(),
        'last_name' => $worksheet->getCell('C'.$row)->getValue(),
        'email' => $worksheet->getCell('D'.$row)->getValue(),
        'password' => $worksheet->getCell('E'.$row)->getValue(),
        'type' => $worksheet->getCell('F'.$row)->getValue()
     ];
}
// muestra json en la pagina
echo json_encode($data);
  $error = '';
  $name = '';
  $last_name = '';
  $email = '';
  $password= '';
 }
}

?>
<!--- HTML FORM--->
<!DOCTYPE html>
<html>
 <head>
  <title>Guardando datos en un .csv</title>
  

 </head>
 <body>
  <br />
  <div class="container">
   <h2 align="center">Creación archivo</h2>
   <br />
   <div class="col-md-6" style="margin:0 auto; float:none;">
    <form method="post">
     <h3 align="center"> Form</h3>
     <br />
     <?php echo $error; ?>
     <div class="form-group">
      <label>Ingrese su nombre</label>
      <input type="text" name="name" placeholder="Nombre" class="form-control"/>
     </div>
     <div class="form-group">
      <label>Ingrese su apellido</label>
      <input type="text" name="last_name" placeholder="Apellido" class="form-control"  />
     </div>
     <div class="form-group">
      <label>Ingrese Email</label>
      <input type="text" name="email" class="form-control" placeholder="Ingrese Email"  />
     </div>
     <div class="form-group">
      <label>Ingrese Password</label>
      <input type="password" name="password" class="form-control" placeholder="Ingrese Contraseña" />
     </div>
   
     <div class="form-group" align="center">
      <input type="submit" name="submit" class="btn btn-info" value="Submit" />
     </div>
    </form>
   </div>
  </div>
 </body>
</html>
