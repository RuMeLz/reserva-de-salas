/* ===== ESTILOS PERSONALIZADOS SISTEMA DE RESERVAS ===== */

/* Variáveis CSS para cores personalizadas */
:root {
  --primary-color: #0d6efd;
  --primary-dark: #0b5ed7;
  --success-color: #198754;
  --danger-color: #dc3545;
  --warning-color: #ffc107;
  --info-color: #0dcaf0;
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

/* Animações suaves */
* {
  transition: all 0.3s ease;
}

/* Estilo do body */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

/* Navbar com gradiente */
.navbar-primary {
  background: var(--gradient-primary) !important;
  box-shadow: var(--shadow-md);
}

.navbar-brand {
  font-weight: 700;
  font-size: 1.5rem;
}

.navbar-brand:hover {
  transform: scale(1.05);
}

/* Cards aprimorados */
.card {
  border: none;
  border-radius: 15px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-header {
  border-bottom: none;
  font-weight: 600;
  padding: 1.25rem 1.5rem;
}

.card-header.bg-primary {
  background: var(--gradient-primary) !important;
}

.card-header.bg-success {
  background: var(--gradient-success) !important;
}

.card-header.bg-danger {
  background: var(--gradient-danger) !important;
}

/* Botões aprimorados */
.btn {
  border-radius: 8px;
  font-weight: 500;
  padding: 0.5rem 1.25rem;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.btn-primary {
  background: var(--gradient-primary);
  border: none;
}

.btn-success {
  background: var(--gradient-success);
  border: none;
}

.btn-danger {
  background: var(--gradient-danger);
  border: none;
}

/* Formulários aprimorados */
.form-control, .form-select {
  border-radius: 8px;
  border: 2px solid #e9ecef;
  padding: 0.75rem 1rem;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  transform: scale(1.01);
}

.input-group-text {
  border-radius: 8px 0 0 8px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border: 2px solid #e9ecef;
  border-right: none;
}

/* Tabelas aprimoradas */
.table {
  border-radius: 10px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.table thead th {
  background: var(--gradient-primary);
  color: white;
  border: none;
  font-weight: 600;
  padding: 1rem;
}

.table tbody tr:hover {
  background-color: rgba(13, 110, 253, 0.1);
  transform: scale(1.01);
}

.table tbody td {
  padding: 1rem;
  vertical-align: middle;
}

/* Badges aprimorados */
.badge {
  padding: 0.5rem 0.75rem;
  border-radius: 20px;
  font-weight: 500;
}

/* Alerts aprimorados */
.alert {
  border-radius: 10px;
  border: none;
  padding: 1.25rem;
  margin-bottom: 1.5rem;
}

.alert-success {
  background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
  color: var(--success-color);
}

.alert-danger {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
  color: var(--danger-color);
}

/* Ícones animados */
.bi {
  transition: transform 0.3s ease;
}

.btn:hover .bi,
.nav-link:hover .bi {
  transform: scale(1.1);
}

/* Spinner personalizado */
.spinner-border {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Efeitos especiais para páginas de sucesso/erro */
.display-1 {
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-20px);
  }
  60% {
    transform: translateY(-10px);
  }
}

/* Responsive melhorado */
@media (max-width: 768px) {
  .card-body {
    padding: 1rem;
  }
  
  .table-responsive {
    border-radius: 10px;
  }
  
  .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .d-md-block .btn {
    width: auto;
    margin-bottom: 0;
  }
}

/* Footer */
footer {
  background: var(--gradient-primary) !important;
  margin-top: auto;
}

/* Efeito de loading nas páginas */
.page-loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* Melhorias de acessibilidade */
.btn:focus,
.form-control:focus,
.form-select:focus {
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

/* Texto gradiente para títulos especiais */
.text-gradient {
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
}