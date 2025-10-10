document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initVideoPlaylist();
});

function initNavbar() {
  const toggle = document.getElementById('navToggle');
  const menu = document.getElementById('navMenu');

  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      menu.classList.toggle('active');
      toggle.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('active');
        toggle.classList.remove('active');
      }
    });
  }

  let lastScroll = 0;
  const navbar = document.querySelector('.navbar');

  if (navbar) {
    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;

      if (currentScroll > 100) {
        navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
      } else {
        navbar.style.boxShadow = 'none';
      }

      lastScroll = currentScroll;
    });
  }
}

function initVideoPlaylist() {
  const videoElement = document.getElementById('heroVideo');

  if (!videoElement || typeof videoPlaylist === 'undefined' || !Array.isArray(videoPlaylist)) {
    return;
  }

  const validVideos = videoPlaylist.filter(src => src && typeof src === 'string');

  if (validVideos.length === 0) {
    console.warn('No valid video files in playlist');
    return;
  }

  let currentIndex = 0;
  let isTransitioning = false;

  function loadVideo(index) {
    if (isTransitioning || index < 0 || index >= validVideos.length) {
      return;
    }

    isTransitioning = true;
    const videoSrc = validVideos[index];

    const tempVideo = document.createElement('video');
    tempVideo.style.display = 'none';
    tempVideo.preload = 'auto';
    tempVideo.muted = true;

    tempVideo.addEventListener('canplaythrough', () => {
      videoElement.style.opacity = '0';

      setTimeout(() => {
        videoElement.src = videoSrc;
        videoElement.load();
        videoElement.play().then(() => {
          videoElement.style.opacity = '1';
          isTransitioning = false;
        }).catch(err => {
          console.error('Video play error:', err);
          isTransitioning = false;
          playNextVideo();
        });
      }, 300);
    }, { once: true });

    tempVideo.addEventListener('error', () => {
      console.error('Error loading video:', videoSrc);
      isTransitioning = false;
      playNextVideo();
    }, { once: true });

    tempVideo.src = videoSrc;
  }

  function playNextVideo() {
    currentIndex = (currentIndex + 1) % validVideos.length;
    loadVideo(currentIndex);
  }

  videoElement.style.transition = 'opacity 0.3s ease-in-out';

  videoElement.addEventListener('ended', playNextVideo);

  videoElement.addEventListener('error', () => {
    console.error('Video playback error, trying next video');
    playNextVideo();
  });

  loadVideo(0);
}

window.addEventListener('load', () => {
  document.body.classList.add('loaded');

  const lazyImages = document.querySelectorAll('img[loading="lazy"]');
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
          }
          imageObserver.unobserve(img);
        }
      });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
  }
});
