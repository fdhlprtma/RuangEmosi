// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mainNav = document.querySelector('.main-nav ul');
  
  if (mobileMenuToggle && mainNav) {
      mobileMenuToggle.addEventListener('click', function() {
          mainNav.style.display = mainNav.style.display === 'flex' ? 'none' : 'flex';
      });
  }
  
  // Auto-hide messages after 5 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
      setTimeout(() => {
          alert.style.opacity = '0';
          setTimeout(() => {
              alert.style.display = 'none';
          }, 300);
      }, 5000);
  });
  
  // Testimonial slider
  const testimonials = document.querySelectorAll('.testimonial');
  if (testimonials.length > 1) {
      let currentTestimonial = 0;
      
      function showTestimonial(index) {
          testimonials.forEach((testimonial, i) => {
              testimonial.style.display = i === index ? 'block' : 'none';
          });
      }
      
      showTestimonial(0);
      
      setInterval(() => {
          currentTestimonial = (currentTestimonial + 1) % testimonials.length;
          showTestimonial(currentTestimonial);
      }, 5000);
  }
  
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
              targetElement.scrollIntoView({
                  behavior: 'smooth'
              });
          }
      });
  });
});

// Mood tracker chart (akan digunakan di halaman mood-tracker.php)
function setupMoodChart() {
  const ctx = document.getElementById('moodChart');
  if (ctx) {
      const moodChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
              datasets: [{
                  label: 'Mood Minggu Ini',
                  data: [3, 2, 4, 3, 5, 4, 6],
                  backgroundColor: 'rgba(108, 99, 255, 0.2)',
                  borderColor: 'rgba(108, 99, 255, 1)',
                  borderWidth: 2,
                  tension: 0.4,
                  fill: true
              }]
          },
          options: {
              scales: {
                  y: {
                      beginAtZero: false,
                      suggestedMin: 1,
                      suggestedMax: 7,
                      ticks: {
                          callback: function(value) {
                              const moods = {
                                  1: 'Sangat Sedih',
                                  2: 'Sedih',
                                  3: 'Agak Sedih',
                                  4: 'Netral',
                                  5: 'Agak Bahagia',
                                  6: 'Bahagia',
                                  7: 'Sangat Bahagia'
                              };
                              return moods[value] || '';
                          }
                      }
                  }
              },
              plugins: {
                  tooltip: {
                      callbacks: {
                          label: function(context) {
                              const moods = {
                                  1: 'Sangat Sedih',
                                  2: 'Sedih',
                                  3: 'Agak Sedih',
                                  4: 'Netral',
                                  5: 'Agak Bahagia',
                                  6: 'Bahagia',
                                  7: 'Sangat Bahagia'
                              };
                              return moods[context.raw] || context.raw;
                          }
                      }
                  }
              }
          }
      });
  }
}

// Panggil fungsi setup chart saat halaman dimuat
document.addEventListener('DOMContentLoaded', setupMoodChart);