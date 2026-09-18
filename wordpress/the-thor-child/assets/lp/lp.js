/* 家族の役割 紐解きコーチング LP - 最小限のJS（スクロール表示・追従CTA） */
(function () {
  var root = document.getElementById('amami-lp');
  if (!root) return;

  // セクションのふわっと表示
  var reveals = root.querySelectorAll('.lp-reveal, .lp-mk');
  if ('IntersectionObserver' in window && reveals.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { rootMargin: '0px 0px -10% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  }

  // 追従CTA: 1つ目のCTA（本文内）が画面の上に消えたら表示し、以降は最後まで表示する
  var sticky = root.querySelector('.lp-sticky');
  var firstCta = root.querySelector('.lp-cta');
  if (sticky && firstCta) {
    var onScroll = function () {
      var passed = firstCta.getBoundingClientRect().bottom < 0;
      sticky.classList.toggle('is-visible', passed);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 上へ戻る矢印: 600px以上スクロールしたら表示
  var totop = root.querySelector('.lp-totop');
  if (totop) {
    var onTop = function () { totop.classList.toggle('is-visible', window.scrollY > 600); };
    window.addEventListener('scroll', onTop, { passive: true });
    onTop();
    totop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  // ハンバーガーメニュー
  var toggle = root.querySelector('.lp-menu__toggle');
  var menu = root.querySelector('.lp-menu');
  if (toggle && menu) {
    var setOpen = function (open) {
      menu.hidden = !open;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? '目次を閉じる' : '目次を開く');
      document.body.style.overflow = open ? 'hidden' : '';
    };
    toggle.addEventListener('click', function () { setOpen(menu.hidden); });
    menu.addEventListener('click', function (e) {
      if (e.target === menu || e.target.closest('.lp-menu__close')) { setOpen(false); return; }
      var link = e.target.closest('a[href^="#"]');
      if (link) {
        var target = root.querySelector(link.getAttribute('href'));
        if (target) {
          e.preventDefault();
          setOpen(false);
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !menu.hidden) setOpen(false); });
  }
})();
