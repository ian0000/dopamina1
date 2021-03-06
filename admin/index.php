<?php
require '../includes/funciones.php';
require '../includes/config/database.php';
$auth = autenticacion();
if (!$auth) {
    header('Location:/');
}


//no creo que es necesario ya que guardo el nombre en la base y de ahi llamo el link nunca uso aws ....creo
//desde aqui llamo al bucket s3 
//defino la direccion que voy a aumentar el nombre para dirigirme ahi
define('IMAGE_URL',"https://s3-demo-dopa.s3.us-east-2.amazonaws.com/");
//llamo a la carpeta que mecrea el composer
require('../vendor/autoload.php');
use Aws\S3\S3Client; 
//libreria de aws para los errores no se a usado aun 
use Aws\Exception\AwsException; 

//llamo a las credenciales 
$s3 = new Aws\S3\S3Client([
    'version'  => 'latest',
    'region'   => 'us-east-2', 
]);

$db = conectarDB();
$query = "SELECT * FROM ropa";
$resultadoConsulta = mysqli_query($db, $query);
$resultado = $_GET['resultado'] ?? null; //revisa que haya un resultado si no hay dara un null


//eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; //este post no existe hasta que se mande el request
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if ($id) {
        $query = "SELECT imagen FROM ropa WHERE id = ${id};";
        $resultado = mysqli_query($db, $query);
        $ropa = mysqli_fetch_assoc($resultado);

        $query = "DELETE FROM ropa WHERE id = ${id};";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header('Location:/admin?resultado=3');
        }
    }
}
incluirTemplate('headerAdmin');
?>

<main class="contenedor seccion tabla">
    <h1>Administrador Ropa</h1>
    <?php if (intval($resultado) === 1) { ?>
        <p class="alerta exito">Ropa creado correctamente</p>
    <?php } else if (intval($resultado) === 2) { ?>
        <p class="alerta exito">Ropa actualizado correctamente</p>
    <?php } else if (intval($resultado) === 3) { ?>
        <p class="alerta exito">Ropa Eliminado correctamente</p>
    <?php } ?>

    <a href="crud/crear.php" class="btn-agregar">Agregar Ropa</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NOMBRE</th>
                <th>IMAGEN</th>
                <th>PRECIO</th>
                <th>CANTIDAD</th>
                <th>DESCUENTO</th>
                <th>DESCRIPCION</th>
                <th>ACCION</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ropa = mysqli_fetch_assoc($resultadoConsulta)) { ?>
                <tr>
                    <td><?php echo $ropa['id']; ?></td>
                    <td><?php echo $ropa['nombre']; ?></td>
                    <?php echo "<td><img src='".IMAGE_URL."".$ropa['imagen']."' class='imagen-small'></td>"?>
                    <td><?php echo $ropa['precio']; ?></td>
                    <td><?php echo $ropa['cantidad']; ?></td>
                    <td><?php echo $ropa['descuento']; ?></td>
                    <td><?php echo $ropa['descripcion']; ?></td>
                    <td>
                        <a href="crud/actualizar.php?id=<?php echo $ropa['id']; ?>" class="btn-verde">actualizar</a>
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $ropa['id']; ?>">
                            <input type="submit" class="btn-rojo" value="Eliminar">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

<?php
incluirTemplate('footer');
?>