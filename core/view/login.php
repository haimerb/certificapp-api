

<html>
<head>
 <link href="main.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<script type="importmap">
    {
      "imports": {
        "@material/web/": "https://esm.run/@material/web/"
      }
    }
  </script>
  <script type="module">
    import '@material/web/all.js';
    import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';

    document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
  </script>
</head>
<body>


<div class="Login" >
  <div class="login-container">
    <div class="login-header">
        <img src="../../logoapex.png" alt="logo" class="login-logo"/>        
        <div class="login-header">
        <!-- <h2 class="md-typescale-display-small" id="colorFont">Inicio de sesión</h2> -->
        <h2  id="colorFont">Inicio de sesión</h2>
          <form name="form" >

                  <div >
                    <div id="formFieldsSize" >
                        <md-filled-text-field  
                            type="text"
                             name="email"
                             id="email"
                             placeholder="Ingrese email o nit"
                             label="Email"                             
                          >

                          </md-filled-text-field>
                      <!-- <small id="emailHelp" class="form-text text-muted">Nunca compartiremos tu correo electrónico con nadie más.</small> -->

                    </div>
                    
                    <div  id="formFieldsSize">

                      <md-filled-text-field 
                       required
                       type="password"
                       name="password"
                       id="password"
                       placeholder="Ingrese password"
                       minlength="6"
                       
                       >
                       </md-filled-text-field>
                    </div>

                    <hr class="mt-3 mb-3"/>

                    <div class="login-btns">

                      <div class="btn-left">
                        <md-filled-button class="btn btn-primary" type="submit"  variant="contained">
                          Entrar
                        </md-filled-button>
                        
                      </div>
                      
                      <div class="btn-right">
                        <md-filled-button variant="contained" class="btn btn-light">Limpiar</md-filled-button>
                      </div>

                  </div>
                  </div>

                  <div class="form-group">
                    <div *ngIf="f.submitted && isLoginFailed" class="alert alert-danger" role="alert">
                      Login failed: 
                    </div>
                  </div>

        </form>

        <div class="alert alert-success" *ngIf="isLoggedIn">
          Logged in as 
        </div>


      </div>


    </div>
  </div>

</div>
<style>
    form {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;            
    }

  </style>
  </body>
  </html>
