<div x-data="repositorio_menu">
    <div class="w-100 menu_repositorio">
        <a class="item" x-on:click="toggleMenu()">
            <span><i class="ft-plus"></i> Home</span>
        </a>
        <div id="menu_dinamico_carpetas"></div>
    </div>

        @script
        <script>
            Alpine.data('repositorio_menu', () => ({
                init() {
                    this.getMenu()

                    // Evento de Livewire
                    Livewire.on('repositorio_update_menu', () => {
                        this.getMenu()
                    })
                },

                async getMenu(){
                    const data = await @this.getCarpetas()
                    if( data ){
                        const carpetas = JSON.parse( data )
                        const content = $(`#menu_dinamico_carpetas`)
                        const html = await this.cargarCarpetasUser(carpetas)
                        content.html( html );
                    }
                },

                cargarCarpetasUser(data, parentId = 0, nivel = 1) {

                    const children = data.filter(item => item.parent === parentId);
                    
                    if (children.length === 0) return '';

                    let html = `<div class="menu_parent_${parentId} ${ parentId!= 0 ? 'd-none' : '' }">`;
                    
                    children.forEach(item => {

                        new_nivel = nivel + 1
                        const hasChildren = data.some(child => child.parent === item.id);
                        const childrenHtml = this.cargarCarpetasUser(data, item.id, new_nivel);
                        
                        // if( hasChildren ){ // tiene hijo
                            html += `<a class="item" x-on:click="toggleMenu( ${item.id} )" style="margin-left:${nivel * 10}px;">
                                        ${ hasChildren ? `<span><i class="ft-plus">` : `` }
                                        </i>${item.nombre}</span>
                                     </a>`;
                        // }else{ // es la ultima carpeta
                        //     html += `<a class="item" style="margin-left:${nivel * 10}px;">
                        //                 <span>${item.nombre}</span>
                        //              </a>`;
                        // }
                        
                        html += childrenHtml;
                    });
                    
                    html += '</div>';
                    return html;
                },

                // muestra uoculta los hijos en el menú
                toggleMenu( id = 0 ){
                    const content = $(`.menu_parent_${id}`)
                    if( content.is(':visible') ){
                        content.addClass('d-none')
                    }else{
                        content.removeClass('d-none')
                    }
                    // va a repositorio y form-carpeta
                    Livewire.dispatch('changeCarpeta', { id })
                },
            }));

            $(document).on('click', '.menu-link.has-children', function(e) {
                e.preventDefault();
                
                const $this = $(this);
                const $submenu = $this.siblings('.submenu');
                const $icon = $this.find('.expand-icon');
                
                // Toggle del submenú actual
                $submenu.slideToggle(300);
                $icon.toggleClass('expanded');
            });

            // Manejador para items sin hijos (opcional)
            $(document).on('click', '.menu-link:not(.has-children)', function(e) {
                e.preventDefault();
                
                // Remover clase active de todos
                $('.menu-link').removeClass('active');
                
                // Agregar clase active al clickeado
                $(this).addClass('active');
                                // Aquí puedes cargar contenido, navegar, etc.
                console.log('Navegando a:', $(this).attr('href'));
            });

        </script>
    @endscript
</div>
