/* 家族の役割 紐解きコーチング LP - 最小限のJS（スクロール表示・追従CTA） */
(function () {
  var root = document.getElementById('amami-lp');
  if (!root) return;

  // セクションのふわっと表示
  var reveals = root.querySelectorAll('.lp-reveal');
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

  // スマホ用の追従CTA: FVを過ぎたら表示、最後のCTAが見えたら隠す
  var sticky = root.querySelector('.lp-sticky');
  var fv = root.querySelector('.lp-fv');
  var lastCta = root.querySelector('.lp-closing .lp-cta');
  if (sticky && fv) {
    var onScroll = function () {
      var pastFv = fv.getBoundingClientRect().bottom < 0;
      var lastVisible = lastCta ? lastCta.getBoundingClientRect().top < window.innerHeight : false;
      sticky.classList.toggle('is-visible', pastFv && !lastVisible);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }
})();
