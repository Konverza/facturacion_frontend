function c(s,l,i){let n=0;const a=$("#alert-container"),t=`toast-${n++}`,o=$(`<div id="${t}" role="alert"></div>`);o.addClass("alert flex w-full sm:max-w-sm animate-fade-left overflow-hidden rounded-lg bg-white shadow-md animate-duration-300 dark:bg-gray-900 sm:right-10");let e="",r="green";s==="success"?e=`
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none">
                <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="currentColor" stroke-width="1.5" />
                <path d="M8 12.5L10.5 15L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>`:s==="error"?(e=`
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                <path d="M11.992 15H12.001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 12L12 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>`,r="red"):s==="warning"&&(e=`
            <svg class="size-6 text-white" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" /></svg>`,r="yellow"),o.html(`
        <div class="flex w-12 items-center justify-center bg-${r}-500">
            ${e}
        </div>
        <div class="-mx-3 px-4 py-2 flex items-center justify-between w-full">
            <div class="mx-3">
                <div>
                    <span class="text-${r}-500 font-semibold">
                        ${l}
                    </span>
                    <p class="text-sm text-zinc-600 dark:text-zinc-200">
                        ${i}
                    </p>
                </div>
            </div>
             <button type="button"
                class="ms-auto me-2 bg-white text-gray-600 rounded-lg hover:bg-gray-100 inline-flex items-center justify-center h-8 min-w-8 dark:bg-gray-900 dark:text-white dark:hover:bg-gray-800 border border-gray-300 dark:border-gray-700"
                data-dismiss-target="#${t}" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
              </button>
        </div>`),a.append(o),$(`button[data-dismiss-target="#${t}"]`).click(function(){const d=$(this).attr("data-dismiss-target");$(d).remove()}),setTimeout(()=>{$(`#${t}`).remove()},5e3)}export{c as s};
