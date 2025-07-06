<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Sistema Acadêmico iCampus" />
    <link rel="icon" href="public/recursos/images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="public/recursos/css/geral.css" />
    <title>iCampus</title>
</head>
<body>
    <header>
       <div class="header-content">
            <h1>Seja Bem-Vindo ao</h1>
            <div class="logo-container">
                <img src="public/recursos/images/logo.png" alt="Logo iCampus">
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="card professor">
                <a href="public/recursos/php/login.php?tipo=professor" class="card-link" aria-label="Acesso para Professor">
                    <div class="card-image">
                        <img src="public/recursos/images/professor.png" alt="Ícone representando Professor" />
                    </div>
                    <h3>Professor</h3>
                </a>
            </div>

            <div class="card aluno">
                <a href="public/recursos/php/login.php?tipo=aluno" class="card-link" aria-label="Acesso para Aluno">
                    <div class="card-image">
                        <img src="public/recursos/images/aluno.png" alt="Ícone representando Aluno" />
                    </div>
                    <h3>Aluno</h3>
                </a>
            </div>

            <div class="card adm">
                <a href="public/recursos/php/login.php?tipo=admin" class="card-link" aria-label="Acesso para Administrador">
                    <div class="card-image">
                        <img src="public/recursos/images/adm.png" alt="Ícone representando Administrador" />
                    </div>
                    <h3>Administrador</h3>
                </a>
            </div>
        </div>
    </main>

<footer>
  <details class="footer-details">
    <summary>Saiba mais</summary>
    <div class="footer-columns">
      <div class="footer-section">
        <h4>Fundadores</h4>
        <ul>
          <li><a href="https://www.instagram.com/alcir_jr7/">Alcir José</a></li>
          <li><a href="https://www.instagram.com/grafalt_/">Lincon Fraga</a></li>
          <li><a href="https://www.instagram.com/marylia_gabriela_/">Marília Gabriela</a></li>
          <li><a href="https://www.instagram.com/mika_santos12/">Mikaias Marinho</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h4>Redes Sociais</h4>
        <ul>
          <li><a href="https://github.com/alcir-jr7/portal-academico.git">GitHub</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h4>Ajuda</h4>
        <ul>
          <li>Tutoriais</li>
          <li>Sobre o Projeto</li>
        </ul>
      </div>
    </div>
  </details>

  <div class="footer-bottom">
    <p>&copy; 2025 Sistema Acadêmico</p>
  </div>
</footer>

