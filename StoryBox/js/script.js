(function ($) { 
    'use strict';

    // Ẩn preloader khi trang tải xong
    $(window).on('load', function () {
        $('.preloader').fadeOut(100);
    });

    // Thêm hoặc xóa hiệu ứng shadow cho thanh navbar khi cuộn trang
    $(window).scroll(function () {
        if ($(window).scrollTop() > 50) {
            $('#main-navbar').addClass('shadow');
        } else {
            $('#main-navbar').removeClass('shadow');
        }
    });

    $(document).ready(function() {
        // Xử lý sự kiện click dropdown user
        $('#userDropdown').click(function(e) {
            e.stopPropagation();
            $('#user-info').toggleClass('active');
        });
      
        // Ẩn dropdown khi click ra ngoài
        $(document).click(function() {
            $('#user-info').removeClass('active');
        });
    });

    $(document).ready(function() {
        // Nếu tồn tại carousel câu chuyện
        if ($('#story-carousel').length) {
            const carousel = $('#story-carousel');
            const images = $('#story-carousel img');
            const dotsContainer = $('#carousel-dots');
            const prevBtn = $('.prev-btn');
            const nextBtn = $('.next-btn');

            let currentIndex = 0;
            const visibleItems = 5; // Số lượng ảnh hiển thị cùng lúc
            const imageWidth = images.eq(0).width() + 30; // Chiều rộng ảnh + khoảng cách
            const totalItems = images.length;
            const numDots = Math.max(totalItems - visibleItems + 1, 0); // Số dots

            // Xóa dots cũ trước khi tạo mới
            dotsContainer.empty();

            // Tạo các dots điều hướng
            for (let i = 0; i < numDots; i++) {
                const dot = $('<span class="dot"></span>');
                if (i === 0) dot.addClass('active');
                dot.on('click', () => goToSlide(i));
                dotsContainer.append(dot);
            }

            const dots = $('.dot');

            // Tự động chuyển ảnh mỗi 3 giây
            let interval = setInterval(nextSlide, 3000);

            function updateCarousel() {
                carousel.css('transform', `translateX(-${currentIndex * imageWidth}px)`);
                
                // Cập nhật dot active
                dots.removeClass('active').eq(currentIndex).addClass('active');
            }

            function nextSlide() {
                if (currentIndex < totalItems - visibleItems) { 
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateCarousel();
            }

            function prevSlide() {
                if (currentIndex > 0) { 
                    currentIndex--;
                } else {
                    currentIndex = totalItems - visibleItems;
                }
                updateCarousel();
            }

            function goToSlide(index) {
                currentIndex = index;
                updateCarousel();
                resetInterval();
            }

            function resetInterval() {
                clearInterval(interval);
                interval = setInterval(nextSlide, 3000);
            }

            // Xử lý sự kiện click nút next và prev
            nextBtn.on('click', function() {
                nextSlide();
                resetInterval();
            });

            prevBtn.on('click', function() {
                prevSlide();
                resetInterval();
            });

            // Dừng tự động chuyển ảnh khi hover vào carousel
            carousel.on('mouseenter', () => clearInterval(interval));
            carousel.on('mouseleave', () => resetInterval());
        }
    });    

    $(document).ready(function() {
        function adjustContentHeight() {
            var windowHeight = $(window).height();
            var headerHeight = $('#admin-header').outerHeight();
            var contentHeight = windowHeight - headerHeight;
            
            $('#admin-main').css('height', contentHeight);
        }
    
        adjustContentHeight();
        $(window).resize(adjustContentHeight);
    });    

    // Client-side validation
    $('#auth-form').on('submit', function(e) {
        let valid = true;
        const password = $('#password').val();
        
        if (password === '') {
            alert('Vui lòng nhập mật khẩu!');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();  // Dừng việc submit form nếu có lỗi
        }
    }); 
    
    $(document).ready(function() {
        $('#follow-form').submit(function(e) {
            e.preventDefault();
            
            // Hiển thị loading state (tuỳ chọn)
            const button = $('#story-button');
            button.prop('disabled', true).text('Đang xử lý...');
            
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Cập nhật trạng thái nút
                        if (response.is_following) {
                            button.removeClass('not-followed').addClass('followed')
                                 .text('❤︎ Đang theo dõi');
                            $('input[name="action"]').val('unfollow');
                        } else {
                            button.removeClass('followed').addClass('not-followed')
                                 .text('+ Theo dõi');
                            $('input[name="action"]').val('follow');
                        }
                        
                        // Cập nhật số lượt follow
                        $('#follow-count strong').text(response.follow_count.toLocaleString());
                        
                        // Cập nhật CSRF token mới nếu có
                        if (response.new_token) {
                            $('input[name="csrf_token"]').val(response.new_token);
                        }
                    } else {
                        alert(response.error || 'Có lỗi xảy ra');
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    try {
                        const error = JSON.parse(xhr.responseText).error;
                        alert("Lỗi: " + error);
                    } catch (e) {
                        alert("Lỗi hệ thống, vui lòng thử lại");
                    }
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        });
    });

    // Xử lý nút reply comment
    $(document).ready(function() {
        $('.reply-btn').click(function(e) {
            e.preventDefault();
            var commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).show();
        });
        
        $('.cancel-reply').click(function() {
            $(this).closest('.reply-form').hide();
        });
    });

    // ADMIN    
    $(function() {
        // ========== XỬ LÝ CHUNG ==========
        
        // Xác nhận trước khi thực hiện hành động quan trọng
        $(document).on('submit', 'form[data-confirm]', function(e) {
            if (!confirm($(this).data('confirm'))) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    
        // Xử lý phân trang
        $(document).ready(function() {
            // Xử lý sự kiện click phân trang
            $(document).on('click', '#pagination .page-btn:not(.disabled)', function() {
                const page = $(this).data('page');
                if (!page) return;
                
                goToPage(page);
            });

            // Hàm chuyển trang
            function goToPage(page) {
                const url = new URL(window.location.href);
                
                // Giữ lại tất cả các tham số hiện có
                const currentParams = Object.fromEntries(url.searchParams.entries());
                
                // Cập nhật trang mới
                currentParams.page = page;
                
                // Xây dựng lại URL
                const newUrl = url.pathname + '?' + $.param(currentParams);
                
                window.location.href = newUrl;
            }

            // Cuộn lên đầu trang khi chuyển trang (tuỳ chọn)
            if (window.location.search.includes('page=')) {
                $('html, body').animate({ scrollTop: 0 }, 'fast');
            }
        });
        
        // Modal chung
        $(document).on('click', '.modal-close, .btn-cancel', function() {
            $(this).closest('.modal').fadeOut(200);
        });
    
        // Đóng modal khi click bên ngoài
        $(document).on('click', '.modal', function(e) {
            if (e.target === this) $(this).fadeOut(200);
        }).on('click', '.modal-content', function(e) {
            e.stopPropagation();
        });
    
        // ========== QUẢN LÝ TRUYỆN ==========
        if ($('#story-grid').length) {
            // Hiển thị modal cập nhật
            window.showUpdateOptions = function(storyId) {
                // Cập nhật href cho các option
                $('#update-info-option').attr('href', 'update-story-info.php?id=' + storyId);
                $('#update-chapter-option').attr('href', 'update-story-chapter.php?id=' + storyId);
                
                // Hiển thị modal
                $('#updateModal')
                    .data('story-id', storyId)
                    .fadeIn(200);
            };

            // Xử lý đóng modal
            window.closeModal = function() {
                $('#updateModal').fadeOut(200);
            };
    
            // Xác nhận xóa truyện
            $(document).on('submit', '.delete-story-form', function(e) {
                return confirm('Bạn có chắc chắn muốn xóa truyện này?');
            });
        }
    
        // ========== QUẢN LÝ NGƯỜI DÙNG ==========
        if ($('#user-grid').length) {
            // Thiết lập thông báo xác nhận
            $('.btn-delete').closest('form').attr('data-confirm', 'Bạn có chắc muốn xóa người dùng này?');
            $('.btn-lock').closest('form').attr('data-confirm', 'Bạn có chắc muốn khóa tài khoản này?');
            $('.btn-unlock').closest('form').attr('data-confirm', 'Bạn có chắc muốn mở khóa tài khoản này?');
        }

        // Upload ảnh
        $(document).ready(function() {
            // Xử lý preview ảnh bìa
            $('#cover-image-input').change(function(e) {
              const file = e.target.files[0];
              if (file) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                  $('#cover-preview-img').attr('src', event.target.result);
                }
                
                reader.readAsDataURL(file);
              }
            });
            
            // Xử lý khi click nút hủy
            $('.btn-cancel').click(function(e) {
              e.preventDefault();
              $('#cover-preview-img').attr('src', 'images/default-cover.jpg');
              $('#cover-image-input').val('');
            });
          });
    });

})(jQuery);
