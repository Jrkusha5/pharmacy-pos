<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Pharmacy Management')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>

    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["{{ asset('assets/css/fonts.min.css') }}"],
        },
        active: () => { sessionStorage.fonts = true; },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <!-- Demo CSS (remove in production) -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    @stack('styles')
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      @include('partials.sidebar')
      <!-- End Sidebar -->

      <div class="main-panel">
        @include('partials.header')

        <div class="container">
          <div class="page-inner">
            @yield('content')
          </div>
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    @stack('scripts')
    
    <!-- Notification System -->
    <script>
      // Load notifications on page load
      $(document).ready(function() {
        loadNotifications();
        loadUnreadCount();
        
        // Refresh notifications every 60 seconds
        setInterval(function() {
          loadUnreadCount();
        }, 60000);
      });

      function loadNotifications() {
        $.get('{{ route("notifications.index") }}?unread=1&limit=5', function(data) {
          var html = '';
          if (data.notifications && data.notifications.data && data.notifications.data.length > 0) {
            data.notifications.data.forEach(function(notif) {
              var iconClass = notif.type == 'expiry' ? 'fa-calendar-times' : 'fa-exclamation-triangle';
              var levelClass = notif.level == 'critical' ? 'notif-danger' : (notif.level == 'warning' ? 'notif-warning' : 'notif-primary');
              var timeAgo = getTimeAgo(notif.created_at);
              
              html += '<a href="#" onclick="markAsRead(' + notif.id + '); return false;">';
              html += '<div class="notif-icon ' + levelClass + '">';
              html += '<i class="fa ' + iconClass + '"></i>';
              html += '</div>';
              html += '<div class="notif-content">';
              html += '<span class="block">' + notif.title + '</span>';
              html += '<span class="time">' + timeAgo + '</span>';
              html += '</div>';
              html += '</a>';
            });
          } else {
            html = '<div class="text-center p-3 text-muted">No new notifications</div>';
          }
          $('#notification-list').html(html);
        }).fail(function() {
          $('#notification-list').html('<div class="text-center p-3 text-danger">Error loading notifications</div>');
        });
      }

      function loadUnreadCount() {
        $.get('{{ route("notifications.unread-count") }}', function(data) {
          var count = data.count || 0;
          $('#notification-badge').text(count);
          if (count > 0) {
            $('#notification-badge').show();
          } else {
            $('#notification-badge').hide();
          }
        });
      }

      function markAsRead(id) {
        $.post('{{ route("notifications.mark-as-read", ":id") }}'.replace(':id', id), {
          _token: '{{ csrf_token() }}'
        }, function(data) {
          loadNotifications();
          loadUnreadCount();
        });
      }

      function markAllAsRead() {
        $.post('{{ route("notifications.mark-all-read") }}', {
          _token: '{{ csrf_token() }}'
        }, function(data) {
          loadNotifications();
          loadUnreadCount();
        });
      }

      function getTimeAgo(dateString) {
        var date = new Date(dateString);
        var now = new Date();
        var diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return diff + ' seconds ago';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        return Math.floor(diff / 86400) + ' days ago';
      }
    </script>
  </body>
</html>
