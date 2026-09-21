<?php
$role = isset($camsRole) ? $camsRole : 'User';
$home = isset($camsHome) ? $camsHome : '';
$profile = isset($camsProfile) ? $camsProfile : '';
$logout = isset($camsLogout) ? $camsLogout : '';
$nav = isset($camsNav) && is_array($camsNav) ? $camsNav : array();
$image = isset($this->session->image) && $this->session->image ? $this->session->image : 'default.png';
$displayName = trim((string)$this->session->fname . ' ' . (string)$this->session->lname);
$mustChangePassword = (int) $this->session->userdata('must_change_password') === 1;

$flashSuccess = '';
$flashError = '';
foreach (array('SUCCESSMSG', 'success', 'successR') as $flashKey) {
    $flashValue = $this->session->flashdata($flashKey);
    if (!$flashSuccess && $flashValue) $flashSuccess = (string) $flashValue;
}
foreach (array('error', 'error1', 'errormsg', 'errormsg1', 'errormsg2') as $flashKey) {
    $flashValue = $this->session->flashdata($flashKey);
    if (!$flashError && $flashValue) $flashError = (string) $flashValue;
}

if (!function_exists('cams_legacy_icon')) {
    function cams_legacy_icon($name) {
        $icons = array(
            'dashboard' => 'fa-dashboard',
            'users' => 'fa-users',
            'clinic' => 'fa-medkit',
            'appointment' => 'fa-calendar',
            'schedule' => 'fa-calendar-o',
            'tools' => 'fa-cogs',
            'reports' => 'fa-bar-chart',
            'logs' => 'fa-clipboard'
        );
        return isset($icons[$name]) ? $icons[$name] : 'fa-circle-o';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CAMS</title>

  <script>
    (function () {
      try {
        var theme = localStorage.getItem('cams-theme') || 'light';
        if (theme !== 'dark' && theme !== 'light') theme = 'light';
        document.documentElement.setAttribute('data-cams-theme', theme);
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.style.colorScheme = theme;
      } catch (e) {
        document.documentElement.setAttribute('data-cams-theme', 'light');
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?=base_url()?>assets/fonts/google-sans/google-sans.css?v=<?=@filemtime(FCPATH.'assets/fonts/google-sans/google-sans.css')?>">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      important: true,
      corePlugins: { preflight: false },
      safelist: [
        '!px-4','!pt-6','!pb-2','sm:!px-6','lg:!px-8','!pb-10',
        '!rounded-2xl','!border','!border-slate-200','!bg-white','!shadow-sm',
        'dark:!border-slate-800','dark:!bg-slate-900','!border-slate-100','!px-5','!py-4',
        '!p-5','!text-slate-700','dark:!text-slate-200','!bg-slate-50',
        '!min-h-11','!rounded-xl','!bg-white','!px-3.5','!text-sm','!text-base','!text-slate-800','!shadow-none',
        'focus:!border-emerald-500','focus:!ring-4','focus:!ring-emerald-500/10',
        'dark:!border-slate-700','dark:!bg-slate-950','dark:!text-slate-100',
        '!mb-2','!block','!text-xs','!font-bold','!text-slate-600','dark:!text-slate-300',
        '!inline-flex','!min-h-10','!items-center','!justify-center','!gap-2','!px-4','!py-2','!font-semibold','!transition',
        '!border-emerald-700','!bg-emerald-700','!text-white','hover:!border-emerald-800','hover:!bg-emerald-800','hover:!text-white',
        'hover:!bg-slate-50','dark:!bg-slate-800','dark:hover:!bg-slate-700',
        '!border-amber-500','!bg-amber-500','hover:!bg-amber-600',
        '!border-rose-600','!bg-rose-600','hover:!bg-rose-700',
        '!overflow-x-auto','!w-full','!border-collapse','!border-b','!px-4','!py-3',
        '!text-[11px]','!uppercase','!tracking-wider','!text-slate-500',
        'dark:!bg-slate-800/70','dark:!text-slate-400','!border-t',
        '!rounded-3xl','!shadow-2xl','!px-6','!py-5','!p-6','!py-4','!font-medium','!overflow-hidden',
        '!bg-slate-950','!font-heading','!text-2xl','!font-extrabold','!tracking-tight','!text-slate-900','dark:!text-white'
      ],
      theme: {
        extend: {
          fontFamily: {
            sans: ['Google Sans', 'Arial', 'sans-serif'],
            heading: ['Google Sans', 'Arial', 'sans-serif']
          },
          colors: {
            cams: {
              50: '#eefbf5',
              100: '#d7f5e8',
              500: '#1f9d70',
              600: '#16845e',
              700: '#126b4d',
              900: '#0b2d23'
            }
          },
          boxShadow: {
            soft: '0 14px 40px rgba(15,23,42,.08)'
          }
        }
      }
    };
  </script>
  <script>
    document.addEventListener('alpine:init', function () {
      Alpine.store('camsModal', {
        open: false,
        title: '',
        eyebrow: '',
        description: '',
        maxWidth: '48rem',
        locked: false,
        show: function (meta) {
          meta = meta || {};
          this.title = meta.title || 'Manage record';
          this.eyebrow = meta.eyebrow || 'Manage';
          this.description = meta.description || '';
          this.maxWidth = meta.maxWidth || '48rem';
          this.locked = meta.locked === true;
          var modalRoot = document.querySelector('.cams-alpine-modal-root');
          if (modalRoot) modalRoot.removeAttribute('hidden');
          this.open = true;
          document.body.classList.add('overflow-hidden');
        },
        close: function () {
          if (this.locked) return;
          this.open = false;
          document.body.classList.remove('overflow-hidden');
          window.dispatchEvent(new CustomEvent('cams:alpine-modal-close'));
          var body = document.getElementById('cams-alpine-modal-body');
          if (body && !body.hasAttribute('data-cams-preserve-on-close')) body.innerHTML = '';
        }
      });
    });
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/toastr/toastr.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/timepicker/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?=base_url()?>vendors/datatables/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url()?>vendors/datatables/css/buttons.bootstrap.css">
  <link rel="stylesheet" href="<?=base_url()?>css/cams-booking.css?v=<?=@filemtime(FCPATH.'css/cams-booking.css')?>">
  <link rel="stylesheet" href="<?=base_url()?>css/cams-modern.css?v=<?=@filemtime(FCPATH.'css/cams-modern.css')?>">
  <link rel="stylesheet" href="<?=base_url()?>css/cams-select2.css?v=<?=@filemtime(FCPATH.'css/cams-select2.css')?>">

  <script src="<?=base_url()?>assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/fastclick/lib/fastclick.js"></script>
  <script src="<?=base_url()?>assets/dist/js/adminlte.min.js"></script>
  <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?=base_url()?>assets/plugins/toastr/toastr.min.js"></script>
  <script src="<?=base_url()?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/select2/dist/js/select2.full.min.js"></script>
  <script src="<?=base_url()?>js/cams-select2.js?v=<?=@filemtime(FCPATH.'js/cams-select2.js')?>"></script>
  <script src="<?=base_url()?>js/cams-ui.js?v=<?=@filemtime(FCPATH.'js/cams-ui.js')?>"></script>
  <script src="<?=base_url()?>js/cams-tailwind-ui.js?v=<?=@filemtime(FCPATH.'js/cams-tailwind-ui.js')?>"></script>
  <script src="<?=base_url()?>js/cams-alpine-modals.js?v=<?=@filemtime(FCPATH.'js/cams-alpine-modals.js')?>"></script>
  <script src="<?=base_url()?>js/cams-ajax-transactions.js?v=<?=@filemtime(FCPATH.'js/cams-ajax-transactions.js')?>"></script>
  <script src="<?=base_url()?>js/cams-management-table.js?v=<?=@filemtime(FCPATH.'js/cams-management-table.js')?>"></script>
  <style>
    [x-cloak]{display:none!important}
    html body .cams-alpine-modal-root[hidden]{display:none!important}
  </style>
</head>
<body class="hold-transition skin-green sidebar-mini cams-app font-sans bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
<div class="wrapper">
  <?php if ($flashSuccess || $flashError): ?>
    <script>
      window.__CAMS_FLASH_TOAST_ACTIVE = true;
      window.addEventListener('DOMContentLoaded', function () {
        if (!window.CamsUI || typeof window.CamsUI.notify !== 'function') return;
        <?php if ($flashError): ?>
          CamsUI.notify('error', 'Action failed', <?=json_encode($flashError)?>);
        <?php else: ?>
          CamsUI.notify('success', 'Success', <?=json_encode($flashSuccess)?>);
        <?php endif; ?>
        window.__CAMS_FLASH_TOAST_ACTIVE = false;
      });
    </script>
  <?php endif; ?>

  <?php if ($mustChangePassword): ?>
    <a id="cams-required-password-modal"
       class="cams-modal-form-link hidden"
       data-modal-title="Update Password"
       data-modal-locked="true"
       href="<?=base_url('account-tools/password')?>"
       aria-hidden="true"
       tabindex="-1"></a>
    <script>
      (function () {
        var opened = false;
        function openRequiredPasswordModal() {
          if (opened) return;
          var link = document.getElementById('cams-required-password-modal');
          if (!link || !window.jQuery) return;
          opened = true;
          window.jQuery(link).trigger('click');
        }

        document.addEventListener('alpine:initialized', function () {
          window.setTimeout(openRequiredPasswordModal, 0);
        });
        window.addEventListener('load', function () {
          window.setTimeout(openRequiredPasswordModal, 50);
        });
      })();
    </script>
  <?php endif; ?>

  <div hidden
       x-cloak
       x-init="$el.removeAttribute('hidden')"
       x-show.important="$store.camsModal && $store.camsModal.open"
       x-transition.opacity
       @keydown.escape.window="$store.camsModal && $store.camsModal.close()"
       class="cams-alpine-modal-root fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="$store.camsModal.close()"></div>
    <div x-show.important="$store.camsModal.open" x-transition
         :style="{ maxWidth: $store.camsModal.maxWidth }"
         class="relative z-10 w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
      <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-700">
        <div>
          <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600" x-text="$store.camsModal.eyebrow"></p>
          <h3 class="m-0 text-xl font-extrabold tracking-tight text-slate-900 dark:text-white" x-text="$store.camsModal.title"></h3>
          <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-show.important="$store.camsModal.description" x-text="$store.camsModal.description"></p>
        </div>
        <button type="button" @click="$store.camsModal.close()"
                x-show.important="!$store.camsModal.locked"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white"
                aria-label="Close modal">
          <i class="fa fa-times"></i>
        </button>
      </div>
      <div id="cams-alpine-modal-body" class="max-h-[78vh] overflow-y-auto p-6 sm:p-7"></div>
    </div>
  </div>

  <header class="main-header border-b border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
    <a href="<?=base_url($home)?>" class="logo !bg-emerald-950 !text-white dark:!bg-slate-950">
      <span class="logo-mini"><b>C</b></span>
      <span class="logo-lg"><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle" alt="CAMS"> <b>CAMS</b></span>
    </a>

    <nav class="navbar navbar-static-top cams-topbar !bg-white dark:!bg-slate-950">
      <div class="cams-topbar-left">
        <a href="#" class="sidebar-toggle cams-sidebar-toggle" data-toggle="push-menu" role="button" aria-label="Collapse or expand sidebar" title="Collapse or expand sidebar">
          <svg class="cams-sidebar-toggle-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <span class="sr-only">Collapse or expand sidebar</span>
        </a>

        <div class="cams-topbar-context">
          <span>Clinics Appointment Management System</span>
          <strong><?=htmlspecialchars($role, ENT_QUOTES, 'UTF-8')?> Portal</strong>
        </div>
      </div>

      <div class="navbar-custom-menu cams-topbar-actions">
        <ul class="nav navbar-nav">
          <li class="cams-theme-control">
            <button type="button" class="cams-theme-toggle" data-cams-theme-toggle aria-label="Switch to dark mode" title="Switch theme">
              <span class="cams-theme-toggle-icon" aria-hidden="true">
                <i class="fa fa-moon-o cams-theme-icon-dark"></i>
                <i class="fa fa-sun-o cams-theme-icon-light"></i>
              </span>
              <span class="cams-theme-toggle-label hidden-xs">Dark</span>
            </button>
          </li>

          <li class="dropdown user user-menu cams-user-menu">
            <a href="#" class="dropdown-toggle cams-profile-trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img src="<?=base_url('uploads/profile-pic/'.$image)?>" class="user-image" alt="User Image" data-cams-profile-avatar>
              <span class="cams-profile-copy hidden-xs">
                <strong data-cams-profile-name><?=htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8')?></strong>
                <small><?=htmlspecialchars($role, ENT_QUOTES, 'UTF-8')?></small>
              </span>
              <i class="fa fa-angle-down cams-profile-caret hidden-xs" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu cams-profile-menu">
              <li class="user-header">
                <img src="<?=base_url('uploads/profile-pic/'.$image)?>" class="img-circle" alt="User Image" data-cams-profile-avatar>
                <p>
                  <span data-cams-profile-name><?=htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8')?></span>
                  <small><?=htmlspecialchars($role, ENT_QUOTES, 'UTF-8')?> account</small>
                </p>
              </li>
              <li class="user-footer">
                <a href="<?=base_url($profile)?>" class="btn btn-default"><i class="fa fa-user"></i> Profile</a>
                <a href="<?=base_url($logout)?>" class="btn btn-default"><i class="fa fa-sign-out"></i> Sign out</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar !bg-emerald-950 text-emerald-50 dark:!bg-slate-950">
    <section class="sidebar">
      <div class="user-panel cams-sidebar-user-card !rounded-2xl !border !border-white/10 !bg-white/5 !shadow-none backdrop-blur">
        <div class="image">
          <img src="<?=base_url('uploads/profile-pic/'.$image)?>" class="img-circle" alt="User Image" data-cams-profile-avatar>
        </div>
        <div class="info">
          <p data-cams-profile-name><?=htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8')?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> <?=htmlspecialchars($role, ENT_QUOTES, 'UTF-8')?></a>
        </div>
      </div>

      <ul class="sidebar-menu space-y-1" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <?php foreach ($nav as $item): ?>
          <?php if (!empty($item['children'])): ?>
            <li class="treeview">
              <a href="#">
                <i class="fa <?=cams_legacy_icon(isset($item['icon']) ? $item['icon'] : 'dashboard')?>"></i>
                <span><?=htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8')?></span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <?php foreach ($item['children'] as $child): ?>
                  <li>
                    <a<?=!empty($child['modal']) ? ' class="cams-modal-form-link" data-modal-title="'.htmlspecialchars($child['modal'], ENT_QUOTES, 'UTF-8').'"' : ''?> href="<?=base_url($child['url'])?>">
                      <i class="fa fa-circle-o"></i> <?=htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8')?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php else: ?>
            <li>
              <a href="<?=base_url($item['url'])?>">
                <i class="fa <?=cams_legacy_icon(isset($item['icon']) ? $item['icon'] : 'dashboard')?>"></i>
                <span><?=htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8')?></span>
              </a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </section>
  </aside>
