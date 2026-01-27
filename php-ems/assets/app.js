(function(){
  const btn = document.querySelector('[data-sidebar-toggle]');
  const sidebar = document.getElementById('sidebar');
  if (!btn || !sidebar) return;
  btn.addEventListener('click', () => sidebar.classList.toggle('open'));
})();
