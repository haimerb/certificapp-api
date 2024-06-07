<div class="Principal">
<div class="landing-container">

  <div class="menu">
    <nav class="navbar navbar-expand navbar-dark bg-dark">
    <!-- <a href="#" class="navbar-brand">Apex</a> -->
    <img
      class="principal-logo"
      src="../../../assets/logoapex.png"
      alt="logo"/>
    <div class="navbar-nav mr-auto">
      <li class="nav-item">
        <a routerLink="./landing" (click)="goToPrincipal('')" class="nav-link">Inicio</a>
      </li>
      <li class="nav-item">
        <a routerLink="tutorials" class="nav-link">Tutorials</a>
      </li>
      <li class="nav-item">
        <a routerLink="add" class="nav-link">Add</a>
      </li>
      <li class="nav-item">
        <a routerLink="add" class="nav-link">Cambiar Password</a>
      </li>
      <li class="nav-item">
        <a routerLink="add" class="nav-link">Cargar archivos</a>
      </li>
      <li class="nav-item">
        <a routerLink="add" class="nav-link">Descarga de cetificados</a>
      </li>
      <li class="nav-item">
        <a routerLink="add" class="nav-link">Salir</a>
      </li>
    </div>
  </nav>

  </div>


  <div class="principal-container-process">

    <div class="principal-container-process-left">

      <div class="card">

        <h4 class="card-header">Descarga de archivos</h4>

        <div class="card-body">

          <h5 class="card-title">Formulario de descarga</h5>

          <div class="form-group">


            <input type="text" class="form-control form-control-sm" id="nit"  placeholder="Ingrese Nit">
            <br>
            <input type="text" class="form-control form-control-sm" id="nit"  placeholder="Ingrese Nit">
            <br>


            <label for="empresa-asociada">Empresa asociada</label>
            <select class="form-control form-control-sm" id="empresa-asociada">
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
            </select>

            <br>

            <label for="anio">Año</label>
            <select class="form-control form-control-sm" id="anio">
              <option>2024</option>
              <option>2023</option>
              <option>2022</option>
              <option>2021</option>
              <option>2020</option>
            </select>

            <br>

            <label for="exampleFormControlSelect1">Tipo documento</label>
            <select class="form-control form-control-sm" id="exampleFormControlSelect1">
              <option>Ica</option>
              <option>Retefuente</option>
              <option>Iva</option>
            </select>

          </div>

          <a href="#" class="btn btn-primary">Generar</a>

        </div>
      </div>

    </div>

    <div class="principal-container-process-center">

    </div>


    <div class="principal-container-process-right">

      <div class="card">
        <h4 class="card-header">Resultados</h4>
        <div class="card-body">

          <h5 class="card-title">Archivos generados</h5>
          <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
          <a href="#" class="btn btn-primary">Descargar</a>


        </div>
      </div>
    </div>

  </div>

  <!-- <div class="landing-header">
    <img
      class="landing-logo"
      src="../../../assets/logoapex.png"
      alt="logo"
    />

    <p>
      Principal!
      PORTAL PROVEEDORES, CERTITAX Descargue sus certificados Tributarios IVA <code>/</code> RETEFUENTE <code>/</code> ICA
    </p>

    <Button variant="contained"> Iniciar</Button>
  </div> -->



</div>
</div>

