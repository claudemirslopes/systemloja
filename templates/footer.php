</div>
            </div>
        </div>
    </div>    <!-- SB Admin 2 JS e FontAwesome -->    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
window.addEventListener('DOMContentLoaded', function() {
  // Teste jQuery
  if (window.jQuery) {
    document.getElementById('jquery-test').innerText = 'jQuery OK';
  } else {
    document.getElementById('jquery-test').innerText = 'jQuery FALHOU';
  }
  // Teste Modal Bootstrap 5
  document.getElementById('abrirModalTeste').onclick = function() {
    var myModal = new bootstrap.Modal(document.getElementById('modalTeste'));
    myModal.show();
  };
});
    </script>
    <script src="/systemloja/assets/js/scripts.js"></script>
</body>
</html>
