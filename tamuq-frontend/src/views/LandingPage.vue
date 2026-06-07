<template>
  <div class="font-sans antialiased text-slate-600 bg-[#EBF2FA] scroll-smooth">
    
    <nav :class="['fixed w-full z-50 transition-all duration-300', isScrolled ? 'bg-white/90 backdrop-blur-md shadow-md py-3' : 'bg-transparent py-5']">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
          <img 
            src="@/assets/tamuqicon.png" 
            alt="Logo TamuQ" 
            class="w-10 h-10 object-contain " 
          />
          <span :class="['text-2xl font-bold tracking-tight', isScrolled ? 'text-[#00555A]' : 'text-[#00555A]']">
            Tamu<span class="text-[#F26430]">Q</span>
          </span>
        </div>

        <div class="hidden md:flex items-center space-x-8">
          <a href="#beranda" class="text-sm font-medium hover:text-[#F26430] transition-colors">Beranda</a>
          <a href="#fitur" class="text-sm font-medium hover:text-[#F26430] transition-colors">Fitur Unggulan</a>
          <a href="#video" class="text-sm font-medium hover:text-[#F26430] transition-colors">Demo</a>
          <a href="#testimonial" class="text-sm font-medium hover:text-[#F26430] transition-colors">Testimoni</a>
          <a href="#faq" class="text-sm font-medium hover:text-[#F26430] transition-colors">FAQ</a>
        </div>

        <div class="flex items-center gap-3">
            <button @click="$router.push('/login')" class="hidden md:inline-flex px-6 py-2.5 rounded-full bg-[#00555A] text-white font-semibold text-sm hover:bg-[#004448] transition-all shadow-lg hover:shadow-[#00555A]/30">
              Login
            </button>
        </div>
      </div>
    </nav>

    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          
          <div class="text-center lg:text-left space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F26430]/10 text-[#F26430] text-sm font-bold">
              <span class="w-2 h-2 rounded-full bg-[#F26430]"></span>
              Sistem Manajemen Perpustakaan JTIK
            </div>
            <h1 class="text-4xl lg:text-6xl font-extrabold text-[#00555A] leading-tight">
              Modernisasi Layanan <br>
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F26430] to-[#ed8936]">Kampus Digital</span>
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
              TamuQ menghadirkan pengalaman tamu dan manajemen perpustakaan yang terintegrasi, cepat, dan berbasis QR Code untuk civitas akademika.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
              <button @click="$router.push('/login')" class="px-8 py-4 rounded-xl bg-[#F26430] text-white font-bold shadow-xl shadow-[#F26430]/20 hover:shadow-[#F26430]/40 hover:-translate-y-1 transition-all duration-300">
                Mulai Sekarang
              </button>
              <a href="#video" class="px-8 py-4 rounded-xl bg-white text-[#00555A] font-bold border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2 justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
                Lihat Demo
              </a>
            </div>
          </div>

          <div class="relative lg:h-[500px] h-[350px] w-full flex items-center justify-center group"
               @mouseenter="stopHeroSlide" 
               @mouseleave="startHeroSlide">
             
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-gradient-to-tr from-[#00555A]/10 to-[#F26430]/10 rounded-full blur-3xl -z-10"></div>
             
             <div class="relative w-full h-full rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-200">
                
                <div class="flex h-full transition-transform duration-700 ease-in-out will-change-transform"
                     :style="{ transform: `translateX(-${currentHeroIndex * 100}%)` }">
                     
                    <div v-for="(img, index) in heroImages" :key="index" class="min-w-full h-full relative">
                        <img :src="img" alt="Kampus Slide" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
                    </div>
                </div>

                <button @click="prevHeroSlide" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-[#00555A] p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button @click="nextHeroSlide" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-[#00555A] p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2 z-20">
                  <button v-for="(_, index) in heroImages" 
                          :key="index"
                          @click="currentHeroIndex = index"
                          :class="['h-2 rounded-full transition-all duration-300 shadow-sm', currentHeroIndex === index ? 'bg-[#F26430] w-8' : 'bg-white/70 w-2 hover:bg-white']">
                  </button>
                </div>
             </div>
          </div>

        </div>
      </div>
    </section>

    <section id="fitur" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-[#F26430] font-bold tracking-wide uppercase text-sm mb-3">Kenapa TamuQ?</h2>
          <h3 class="text-3xl lg:text-4xl font-bold text-[#00555A]">Solusi Cerdas untuk Civitas</h3>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="p-8 rounded-3xl bg-[#EBF2FA] hover:bg-white hover:shadow-xl transition-all duration-300 border border-transparent hover:border-[#F26430]/20 group">
            <div class="w-14 h-14 bg-[#F26430] rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-[#F26430]/30 group-hover:scale-110 transition-transform">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
            </div>
            <h4 class="text-xl font-bold text-[#00555A] mb-3">Scan QR Code</h4>
            <p class="text-slate-500 leading-relaxed">Masuk perpustakaan atau area kampus hanya dengan scan QR. Cepat, tanpa antri, dan tercatat otomatis.</p>
          </div>
          
          <div class="p-8 rounded-3xl bg-[#EBF2FA] hover:bg-white hover:shadow-xl transition-all duration-300 border border-transparent hover:border-[#F26430]/20 group">
            <div class="w-14 h-14 bg-[#00555A] rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-[#00555A]/30 group-hover:scale-110 transition-transform">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <h4 class="text-xl font-bold text-[#00555A] mb-3">Manajemen Pustaka</h4>
            <p class="text-slate-500 leading-relaxed">Cari, pinjam, dan kembalikan buku dengan sistem terpadu. Cek ketersediaan buku secara real-time.</p>
          </div>

          <div class="p-8 rounded-3xl bg-[#EBF2FA] hover:bg-white hover:shadow-xl transition-all duration-300 border border-transparent hover:border-[#F26430]/20 group">
            <div class="w-14 h-14 bg-[#F26430] rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-[#F26430]/30 group-hover:scale-110 transition-transform">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h4 class="text-xl font-bold text-[#00555A] mb-3">Validasi Digital</h4>
            <p class="text-slate-500 leading-relaxed">Sistem validasi surat bebas pustaka dan donasi buku yang transparan dan dapat dilacak statusnya.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="video" class="py-20 relative overflow-hidden">
      <div class="absolute inset-0 bg-[#00555A] skew-y-3 origin-bottom-right z-0 transform translate-y-20"></div>
      
      <div class="max-w-5xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-3xl font-bold text-white mb-8">Preview Demo</h2>
        
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border-8 border-white/10 bg-black aspect-video flex items-center justify-center">
           
           <div v-if="!isVideoPlaying" 
                @click="isVideoPlaying = true"
                class="absolute inset-0 w-full h-full cursor-pointer group z-20">
             
             <img src= "@/assets/thumbnailvid.png"
                  class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-opacity duration-300" 
                  alt="Video Thumb">
             
             <div class="absolute inset-0 flex items-center justify-center">
               <div class="relative z-10 w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                 <div class="w-14 h-14 bg-[#F26430] rounded-full flex items-center justify-center shadow-lg pl-1">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                   </svg>
                 </div>
               </div>
             </div>
             
             <p class="absolute bottom-10 w-full text-center text-white font-semibold tracking-wider opacity-80 group-hover:opacity-100 transition-opacity">
               Klik untuk Memutar Demo
             </p>
           </div>

           <div v-else class="w-full h-full">
             <video controls autoplay class="w-full h-full object-cover">
                <source src="@/assets/web_demo.mp4" type="video/mp4">
                Browser Anda tidak mendukung tag video.
             </video>
           </div>

        </div>
      </div>
    </section>

    <section id="testimonial" class="py-20 bg-[#EBF2FA] relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
          <h2 class="text-[#F26430] font-bold tracking-wide uppercase text-sm mb-3">Testimoni</h2>
          <h3 class="text-3xl lg:text-4xl font-bold text-[#00555A]">Kata Mereka Tentang TamuQ</h3>
        </div>

        <div class="relative min-h-[450px]">
          <transition name="fade" mode="out-in">
            <div :key="currentTestimonialIndex" class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
              
              <div class="relative flex justify-center order-1 lg:order-1">
                <img :src="testimonials[currentTestimonialIndex].image" 
                     :alt="testimonials[currentTestimonialIndex].name" 
                     class="rounded-2xl shadow-2xl max-w-sm w-full h-[400px] object-cover z-10 bg-slate-300">
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-[#F26430]/20 rounded-2xl -z-0"></div>
                <div class="absolute top-10 -left-10 w-20 h-20 bg-[#00555A]/10 rounded-full blur-xl"></div>
              </div>

              <div class="space-y-6 order-2 lg:order-2">
                <div class="flex items-center gap-2 mb-2">
                   <span class="text-6xl text-[#F26430]/30 font-serif leading-none">"</span>
                </div>
                
                <blockquote class="text-xl lg:text-2xl text-slate-600 italic leading-relaxed">
                  {{ testimonials[currentTestimonialIndex].quote }}
                </blockquote>
                
                <div class="pt-4 border-l-4 border-[#F26430] pl-6">
                  <p class="font-bold text-[#00555A] text-2xl">{{ testimonials[currentTestimonialIndex].name }}</p>
                  <p class="text-[#F26430] font-semibold">{{ testimonials[currentTestimonialIndex].role }}</p>
                </div>

                <div class="flex items-center gap-4 pt-6">
                  <button @click="prevTestimonial" class="p-3 rounded-full border border-[#00555A] text-[#00555A] hover:bg-[#00555A] hover:text-white transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                  </button>
                  
                  <div class="flex gap-2">
                    <button v-for="(_, idx) in testimonials" :key="idx" 
                          @click="currentTestimonialIndex = idx"
                          :class="['h-2 rounded-full transition-all duration-300', idx === currentTestimonialIndex ? 'w-8 bg-[#F26430]' : 'w-2 bg-slate-300 hover:bg-slate-400']">
                    </button>
                  </div>
                  
                  <button @click="nextTestimonial" class="p-3 rounded-full border border-[#00555A] text-[#00555A] hover:bg-[#00555A] hover:text-white transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                  </button>
                </div>
              </div>
            
            </div>
          </transition>
        </div>

      </div>
    </section>

    <section class="py-20 bg-[#EBF2FA] text-center border-t border-slate-200">
      <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-4xl lg:text-5xl font-extrabold text-[#00555A] mb-8 leading-tight">
          Siap untuk Pengalaman Kampus Digital yang Lebih Baik?
        </h2>
        <button @click="$router.push('/login')" class="px-8 py-4 rounded bg-[#F26430] text-white font-bold shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          Daftar Sekarang
        </button>
      </div>
    </section>

    <section id="faq" class="py-20 bg-white">
      <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-[#00555A] text-center mb-12">Pertanyaan Umum (FAQ)</h2>
        
        <div class="space-y-4">
          <div v-for="(item, index) in faqs" :key="index" class="border border-slate-200 rounded-xl overflow-hidden">
            <button @click="toggleFaq(index)" class="w-full flex items-center justify-between p-5 text-left bg-slate-50 hover:bg-slate-100 transition-colors">
              <span class="font-bold text-[#00555A]">{{ item.question }}</span>
              <span class="text-[#F26430] text-xl font-bold transform transition-transform" :class="item.open ? 'rotate-45' : ''">+</span>
            </button>
            <div v-show="item.open" class="p-5 bg-white text-slate-500 border-t border-slate-100">
              {{ item.answer }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-[#001021] text-white pt-20 pb-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-12 mb-16">
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center gap-2 mb-6">
               <img 
                  src="@/assets/unmlogo.png" 
                  alt="Logo TamuQ" 
                  class="w-10 h-10 rounded-lg shadow-lg object-contain bg-[#F26430]" 
              />
               <span class="text-2xl font-bold">Tamu<span class="text-[#F26430]">Q</span></span>
            </div>
            <p class="text-slate-400 max-w-sm mb-6">
              Platform manajemen perpustakaan digital terintegrasi untuk mendukung kegiatan akademik yang lebih efisien.
            </p>
          </div>

          <div>
            <h4 class="text-lg font-bold mb-6 text-[#F26430]">Hubungi Kami</h4>
            <ul class="space-y-4 text-sm text-slate-300">
              <li class="flex items-center gap-3">+62 812-3456-7890</li>
              <li class="flex items-center gap-3">helpdesk@kampus.ac.id</li>
              <li class="flex items-center gap-3">Jalan Andi Pangeran Pettarani, Parang Tambung, Kec. Tamalate, Kota Makassar, Sulawesi Selatan 90224</li>
            </ul>
          </div>

          <div>
            <h4 class="text-lg font-bold mb-6 text-[#F26430]">Lokasi</h4>
             <div class="rounded-2xl overflow-hidden shadow-lg border-2 border-white/10 h-48 w-full relative bg-slate-800">
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.483806427771!2d119.42671487485929!3d-5.1863575947911595!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbee27a4279744d%3A0xa2bf38a72585b647!2sMakassar%20State%20University%20(Parangtambung%20Campus)!5e0!3m2!1sen!2sid!4v1771097124076!5m2!1sen!2sid"
                  title="Peta Lokasi Universitas Negeri Makassar"
                  width="100%" height="100%" style="border:0;" loading="lazy" 
                  class="filter grayscale-[0.3] opacity-90"
                ></iframe>
             </div>
          </div>
        </div>
        <div class="border-t border-white/10 pt-8 text-center text-xs text-slate-500">
          <p>© 2024 TamuQ By Ikrams. All rights reserved.</p>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

import orang1Img from '@/assets/orang1.png';
import orang2Img from '@/assets/orang2.png';
import orang3Img from '@/assets/orang3.png';
import orang4Img from '@/assets/orang4.png';
import orang5Img from '@/assets/orang5.png';

import pic1 from '@/assets/pic1.jpeg';
import pic2 from '@/assets/pic2.jpeg';
import pic3 from '@/assets/pic3.jpeg';
import pic4 from '@/assets/pic4.jpeg';

const isScrolled = ref(false);
const handleScroll = () => { isScrolled.value = window.scrollY > 50; };

const isVideoPlaying = ref(false);

const heroImages = [pic1, pic2, pic3, pic4];

const currentHeroIndex = ref(0);
let heroInterval;

const nextHeroSlide = () => {
  currentHeroIndex.value = (currentHeroIndex.value + 1) % heroImages.length;
};

const prevHeroSlide = () => {
  currentHeroIndex.value = currentHeroIndex.value === 0 
    ? heroImages.length - 1 
    : currentHeroIndex.value - 1;
};

const startHeroSlide = () => {
  if (heroInterval) clearInterval(heroInterval);
  heroInterval = setInterval(() => {
    nextHeroSlide();
  }, 4000);
};

const stopHeroSlide = () => {
  if (heroInterval) clearInterval(heroInterval);
};


const currentTestimonialIndex = ref(0);

const testimonials = ref([
  {
    name: "Orang 1",
    role: "Orang 1",
    image: orang1Img,
    quote: "Pengembangan sistem TamuQ menunjukkan kemajuan signifikan dalam digitalisasi kampus. Fitur-fiturnya sangat relevan dengan kebutuhan saat ini dan mempermudah bimbingan."
  },
  {
    name: "Orang 2",
    role: "Orang 2",
    image: orang2Img,
    quote: "Saya mengapresiasi user interface yang bersih dan navigasi yang intuitif. Mahasiswa berhasil menerjemahkan masalah manual menjadi solusi digital yang elegan."
  },
  {
    name: "Orang 3",
    role: "Orang 3",
    image: orang3Img,
    quote: "Keamanan data dan validitas QR Code pada sistem ini sudah teruji dengan baik. Sebuah inovasi yang layak diterapkan secara luas di lingkungan universitas."
  },
  {
    name: "Orang 4",
    role: "Orang 4",
    image: orang4Img,
    quote: "Integrasi antara buku tamu dan perpustakaan adalah nilai tambah utama. Sistem ini sangat membantu monitoring kunjungan mahasiswa secara real-time."
  },
  {
    name: "Orang 5",
    role: "Orang 5",
    image: orang5Img,
    quote: "Setelah melalui serangkaian pengujian black-box dan stress test, TamuQ terbukti stabil dan responsif menangani banyak request sekaligus."
  }
]);

const nextTestimonial = () => {
  currentTestimonialIndex.value = (currentTestimonialIndex.value + 1) % testimonials.value.length;
};

const prevTestimonial = () => {
  currentTestimonialIndex.value = currentTestimonialIndex.value === 0 
    ? testimonials.value.length - 1 
    : currentTestimonialIndex.value - 1;
};

const faqs = ref([
  { question: "Apa itu TamuQ?", answer: "Sistem informasi tamu & perpustakaan digital berbasis QR Code.", open: true },
  { question: "Bagaimana cara dapat akun?", answer: "Mahasiswa aktif otomatis terdaftar. Login pakai NIM.", open: false },
  { question: "Bisa akses dari HP?", answer: "Ya, TamuQ responsif untuk semua perangkat.", open: false },
  { question: "Fungsi Scan QR?", answer: "Untuk presensi perpustakaan tanpa isi buku tamu manual.", open: false },
]);

const toggleFaq = (index) => {
  faqs.value[index].open = !faqs.value[index].open;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  startHeroSlide(); 
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  stopHeroSlide(); 
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>