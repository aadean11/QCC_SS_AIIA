<div id="modalCircleMembers" class="fixed inset-0 z-[200] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold">
                    <i class="fa-solid fa-users-rectangle mr-2"></i>
                    Detail Anggota Circle
                </h3>
                <button type="button" onclick="closeModal('modalCircleMembers')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <div class="p-4 md:p-8">
                <div class="mb-4 md:mb-6">
                    <h4 id="circleMembersName" class="text-xl md:text-2xl font-black text-[#091E6E]"></h4>
                    <p id="circleMembersMeta" class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest mt-1"></p>
                    <p id="circleMembersTheme" class="text-[10px] md:text-xs text-gray-500 font-medium italic mt-1"></p>
                </div>
                <div>
                    <h4 class="text-[10px] md:text-xs font-bold text-[#091E6E] uppercase tracking-widest mb-3 md:mb-4 border-b pb-2">
                        Daftar Anggota Tim (<span id="circleMembersCount">0</span>)
                    </h4>
                    <div id="circleMembersList" class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                    </div>
                </div>
                <div class="mt-6 md:mt-8 pt-4 md:pt-6 border-t">
                    <button type="button" onclick="closeModal('modalCircleMembers')" class="w-full py-3 md:py-4 bg-gray-100 text-gray-500 rounded-xl md:rounded-2xl font-bold uppercase tracking-widest text-[10px] md:text-xs hover:bg-gray-200 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
