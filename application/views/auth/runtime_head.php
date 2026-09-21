<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="icon" type="image/jpeg" href="<?=base_url()?>assets/dist/img/hnumcfi.jpg">
<link rel="shortcut icon" type="image/jpeg" href="<?=base_url()?>assets/dist/img/hnumcfi.jpg">
<link rel="stylesheet" href="<?=base_url()?>assets/fonts/google-sans/google-sans.css?v=<?=@filemtime(FCPATH.'assets/fonts/google-sans/google-sans.css')?>">
<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="<?=base_url()?>css/cams-select2.css?v=<?=@filemtime(FCPATH.'css/cams-select2.css')?>">

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
      '!min-h-11','!rounded-xl','!px-3.5','!text-sm','!text-base','!text-slate-800','!shadow-none',
      'focus:!border-emerald-500','focus:!ring-4','focus:!ring-emerald-500/10',
      'dark:!border-slate-700','dark:!bg-slate-950','dark:!text-slate-100',
      '!mb-2','!block','!text-xs','!font-bold','!text-slate-600','dark:!text-slate-300',
      '!inline-flex','!min-h-10','!items-center','!justify-center','!gap-2','!px-4','!py-2','!font-semibold','!transition',
      '!border-emerald-700','!bg-emerald-700','!text-white','hover:!border-emerald-800','hover:!bg-emerald-800','hover:!text-white',
      'hover:!bg-slate-50','dark:!bg-slate-800','dark:hover:!bg-slate-700',
      '!border-amber-500','!bg-amber-500','hover:!bg-amber-600',
      '!border-rose-600','!bg-rose-600','hover:!bg-rose-700',
      '!overflow-x-auto','!w-full','!border-collapse','!border-b','!py-3',
      '!text-[11px]','!uppercase','!tracking-wider','!text-slate-500',
      'dark:!bg-slate-800/70','dark:!text-slate-400','!border-t',
      '!rounded-3xl','!shadow-2xl','!px-6','!py-5','!p-6','!font-medium','!overflow-hidden',
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
        }
      }
    }
  };
</script>
<script>
  document.addEventListener('alpine:init', function () {
    if (Alpine.store('camsModal')) return;
    Alpine.store('camsModal', {
      open: false,
      title: '',
      eyebrow: '',
      description: '',
      show: function (meta) {
        meta = meta || {};
        this.title = meta.title || 'CAMS';
        this.eyebrow = meta.eyebrow || 'Account';
        this.description = meta.description || '';
        var modalRoot = document.querySelector('.cams-alpine-modal-root');
        if (modalRoot) modalRoot.removeAttribute('hidden');
        this.open = true;
        document.body.classList.add('overflow-hidden');
      },
      close: function () {
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
<style>
  [x-cloak]{display:none!important}
  html body .cams-alpine-modal-root[hidden]{display:none!important}
</style>
