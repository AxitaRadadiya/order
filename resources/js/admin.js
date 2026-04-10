document.addEventListener('DOMContentLoaded', function(){
  // small helper: close flash alerts after 5s
  setTimeout(()=>{
    document.querySelectorAll('.alert').forEach(el=>el.remove());
  }, 5000);
});
