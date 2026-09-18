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
})();
