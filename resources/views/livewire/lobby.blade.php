<div class="min-h-screen relative flex flex-col bg-[#050505] font-sans overflow-hidden text-zinc-300" x-data="{ 
    interests: '',
    startSearch() {
        let url = '{{ route('video-chat') }}';
        if (this.interests.trim()) {
            const interestArray = this.interests.split(',').map(i => i.trim()).filter(i => i);
            const params = new URLSearchParams();
            interestArray.forEach(i => params.append('interests[]', i));
            url += '?' + params.toString();
        }
        window.location.href = url;
    }
}">
    <!-- Clean Grid Background -->
    <div class="absolute inset-0 grid-bg opacity-40 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-brand/[0.02] to-transparent pointer-events-none"></div>

    <!-- Header -->
    <header class="h-20 px-10 flex items-center justify-between relative z-10 border-b border-white/10 bg-black/60 backdrop-blur-md">
        <h1 class="text-xl font-black tracking-tighter text-white">
            RO<span class="text-brand">Connect</span>
        </h1>
        <div class="flex items-center gap-6">
            <span class="text-[10px] font-black tracking-[0.3em] uppercase text-zinc-300">v2.4.9</span>
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-brand/20 border border-brand/40">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                <span class="text-[9px] font-black tracking-widest uppercase text-white">8.2k Students</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center p-6 lg:p-12 relative z-10">
        <div class="w-full max-w-4xl animate-in fade-in slide-in-from-bottom-12 duration-1000">
            
            <!-- Hero Title -->
            <div class="text-center mb-16">
                <h2 class="text-6xl lg:text-8xl font-black text-white tracking-tighter mb-6">
                    DISCOVER <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand via-white to-white/80">WHO'S NEXT.</span>
                </h2>
                <p class="text-sm lg:text-base font-bold text-zinc-200 uppercase tracking-[0.4em] drop-shadow-lg">Instant Video Discovery Network</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-6 mb-12">
                <!-- Feature Card 1 -->
                <div class="feature-card">
                    <div class="w-12 h-12 rounded-2xl bg-brand/20 flex items-center justify-center border border-brand/40">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black tracking-widest uppercase text-zinc-100">Live Video</span>
                </div>
                <!-- Feature Card 2 -->
                <div class="feature-card">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20 text-white">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black tracking-widest uppercase text-zinc-100">Real-time Chat</span>
                </div>
                <!-- Feature Card 3 -->
                <div class="feature-card">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20 text-white">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black tracking-widest uppercase text-zinc-100">Match Interests</span>
                </div>
            </div>

            <!-- Central Hub -->
            <div class="max-w-xl mx-auto glass-panel p-10">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black tracking-[0.4em] uppercase text-zinc-200 text-center block">Search discovery network</label>
                        <input 
                            type="text" 
                            x-model="interests"
                            @keydown.enter="startSearch()"
                            placeholder="Gaming, Music, Tech, Coding..." 
                            class="pro-input py-6 text-center text-lg"
                        >
                    </div>

                    <button 
                        @click="startSearch()"
                        class="pro-button w-full py-6 text-base lg:text-lg flex items-center justify-center gap-4 group"
                    >
                        START JOURNEY
                        <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M5 12h16"></path></svg>
                    </button>
                    
                    <p class="text-center text-[9px] font-bold tracking-[0.3em] uppercase text-zinc-300">Verified ROConnect Student Infrastructure</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="p-8 text-center relative z-10 border-t border-white/10">
        <p class="text-[9px] font-black tracking-[0.5em] uppercase text-zinc-400">ROConnect Discovery Network © 2026</p>
    </footer>

    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fade-in 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</div>
