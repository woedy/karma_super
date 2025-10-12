

const Header = (props: {
  sidebarOpen: string | boolean | undefined;
  setSidebarOpen: (arg0: boolean) => void;
}) => {
  return (
    <>
      <header className="sticky top-0 z-999 flex w-full bg-white drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none">
        <div className="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
          <div className="flex items-center gap-2 sm:gap-4 md:hidden lg:hidden">
            <a
              class="dib pa2 ml3 v-mid grayscale-icon bg-white"
              href="https://turbotax.intuit.com"
              target="_blank"
              rel="noopener noreferrer"
            >
              <img
                srcset="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.100/navi/IC-logo.svg?auto=compress%2Cformat&dpr=1 1x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.100/navi/IC-logo.svg?auto=compress%2Cformat&dpr=2 2x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.100/navi/IC-logo.svg?auto=compress%2Cformat&dpr=3 3x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.100/navi/IC-logo.svg?auto=compress%2Cformat&dpr=4 4x"
                src="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.100/navi/IC-logo.svg?auto=compress%2Cformat"
                width="125"
                height="35"
                role="img"
                title="TurboTax"
                aria-label="TurboTax"
                alt="TurboTax"
              />
            </a>

            <div className="flex items-center gap-4 absolute right-4">

              
              <a class="" href="https://www.creditkarma.com/auth/logon">
                <span class="text-sm">Log in</span>
              </a>

              <a class="" href="https://www.creditkarma.com/signup">
                <span class="text-sm">Sign up</span>
              </a>
          
            {/* <!-- Hamburger Toggle BTN --> */}
            <button
              aria-controls="sidebar"
              onClick={(e) => {
                e.stopPropagation();
                props.setSidebarOpen(!props.sidebarOpen);
              }}
              className="z-99999 rounded-sm bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark lg:hidden"
              >
              <span className="relative block h-5.5 w-5.5 cursor-pointer">
                <span className="du-block absolute right-0 h-full w-full">
                  <span
                    className={`relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-primary delay-[0] duration-200 ease-in-out dark:bg-white ${
                      !props.sidebarOpen && '!w-full delay-300'
                    }`}
                  ></span>
                  <span
                    className={`relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-primary delay-150 duration-200 ease-in-out dark:bg-white ${
                      !props.sidebarOpen && 'delay-400 !w-full'
                    }`}
                  ></span>
                  <span
                    className={`relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-primary delay-200 duration-200 ease-in-out dark:bg-white ${
                      !props.sidebarOpen && '!w-full delay-500'
                    }`}
                  ></span>
                </span>
                <span className="absolute right-0 h-full w-full rotate-45">
                  <span
                    className={`absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-primary delay-300 duration-200 ease-in-out dark:bg-white ${
                      !props.sidebarOpen && '!h-0 !delay-[0]'
                    }`}
                  ></span>
                  <span
                    className={`delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-primary duration-200 ease-in-out dark:bg-white ${
                      !props.sidebarOpen && '!h-0 !delay-200'
                    }`}
                  ></span>
                </span>
              </span>
            </button>

            {/* <!-- Hamburger Toggle BTN --> */}


            </div>

         
          </div>

          <div className="flex gap-3"></div>
        </div>
      </header>

   
    </>
  );
};

export default Header;
