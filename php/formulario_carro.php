<div class="form-group">
    <label for="marca">Marca:</label>
    <select name="marca" id="marca" required>
        <option value="Toyota" <?php if (isset($carro) && $carro['marca'] == 'Toyota') echo 'selected'; ?>>Toyota</option>
        <option value="Ford" <?php if (isset($carro) && $carro['marca'] == 'Ford') echo 'selected'; ?>>Ford</option>
        <option value="Chevrolet" <?php if (isset($carro) && $carro['marca'] == 'Chevrolet') echo 'selected'; ?>>Chevrolet</option>
        <option value="Honda" <?php if (isset($carro) && $carro['marca'] == 'Honda') echo 'selected'; ?>>Honda</option>
        <option value="Nissan" <?php if (isset($carro) && $carro['marca'] == 'Nissan') echo 'selected'; ?>>Nissan</option>
        <option value="BMW" <?php if (isset($carro) && $carro['marca'] == 'BMW') echo 'selected'; ?>>BMW</option>
        <option value="Mercedes-Benz" <?php if (isset($carro) && $carro['marca'] == 'Mercedes-Benz') echo 'selected'; ?>>Mercedes-Benz</option>
        <option value="Volkswagen" <?php if (isset($carro) && $carro['marca'] == 'Volkswagen') echo 'selected'; ?>>Volkswagen</option>
        <option value="Audi" <?php if (isset($carro) && $carro['marca'] == 'Audi') echo 'selected'; ?>>Audi</option>
        <option value="Hyundai" <?php if (isset($carro) && $carro['marca'] == 'Hyundai') echo 'selected'; ?>>Hyundai</option>
    </select>
</div>
<div class="form-group">
    <label for="modelo">Modelo:</label>
    <select name="modelo" id="modelo" required>
        <option value="<?php echo isset($carro) ? $carro['modelo'] : ''; ?>" selected><?php echo isset($carro) ? $carro['modelo'] : ''; ?></option>
    </select>
</div>
<div class="form-group">
    <label for="ano">Año:</label>
    <input type="number" name="ano" id="ano" value="<?php echo isset($carro) ? $carro['ano'] : ''; ?>" required>
</div>
<div class="form-group">
    <label for="asientos">Número de Asientos:</label>
    <input type="number" name="asientos" id="asientos" value="<?php echo isset($carro) ? $carro['asientos'] : ''; ?>" required>
</div>
<div class="form-group">
    <label for="placa">Número de Placa:</label>
    <input type="text" name="placa" id="placa" value="<?php echo isset($carro) ? $carro['placa'] : ''; ?>" required>
</div>
<div class="form-group">
    <label for="combustible">Combustible:</label>
    <select name="combustible" id="combustible" required>
        <option value="Gasolina" <?php if (isset($carro) && $carro['combustible'] == 'Gasolina') echo 'selected'; ?>>Gasolina</option>
        <option value="Diesel" <?php if (isset($carro) && $carro['combustible'] == 'Diesel') echo 'selected'; ?>>Diesel</option>
        <option value="Eléctrico" <?php if (isset($carro) && $carro['combustible'] == 'Eléctrico') echo 'selected'; ?>>Eléctrico</option>
        <option value="Híbrido" <?php if (isset($carro) && $carro['combustible'] == 'Híbrido') echo 'selected'; ?>>Híbrido</option>
    </select>
</div>
<div class="form-group">
    <label for="transmision">Transmisión:</label>
    <select name="transmision" id="transmision" required>
        <option value="Automática" <?php if (isset($carro) && $carro['transmision'] == 'Automática') echo 'selected'; ?>>Automática</option>
        <option value="Manual" <?php if (isset($carro) && $carro['transmision'] == 'Manual') echo 'selected'; ?>>Manual</option>
    </select>
</div>
<div class="form-group">
    <label for="descripcion">Descripción:</label>
    <textarea name="descripcion" id="descripcion" class="form-control" required><?php echo isset($carro) ? $carro['descripcion'] : ''; ?></textarea>
</div>
<div class="form-group">
    <label for="disponible">Disponibilidad:</label>
    <select name="disponible" id="disponible" required>
        <option value="1" <?php if (isset($carro) && $carro['disponible']) echo 'selected'; ?>>Disponible</option>
        <option value="0" <?php if (isset($carro) && !$carro['disponible']) echo 'selected'; ?>>No Disponible</option>
    </select>
</div>
<div class="form-group">
    <label for="precio">Precio:</label>
    <input type="number" name="precio" id="precio" value="<?php echo isset($carro) ? $carro['precio'] : ''; ?>" required>
</div>