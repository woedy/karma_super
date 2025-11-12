import React, { useState } from "react";

const navItems = [
  "Mail & Ship",
  "Track & Manage",
  "Postal Store",
  "Business",
  "International",
  "Help",
];

const Header: React.FC = () => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <header className="w-full bg-gray-100 border-b border-gray-200">
      {/* Utility bar - desktop */}
      <div className="hidden lg:flex items-center justify-end px-6 py-2 text-[0.5rem] text-gray-600 bg-white gap-12">
        <div className="flex items-center gap-4">
          <svg
            className="h-4 w-4 text-blue-900"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.5"
          >
            <circle cx="12" cy="12" r="9" />
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
          </svg>
          <span>English</span>
        </div>
        <div className="flex items-center gap-4">
          <svg
            className="h-4 w-4 text-blue-900"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.5"
          >
            <path d="M12 21s7-5.5 7-11a7 7 0 0 0-14 0c0 5.5 7 11 7 11z" />
            <circle cx="12" cy="10" r="2.5" />
          </svg>
          <span>Locations</span>
        </div>
        <div className="flex items-center gap-4">
          <svg
            className="h-4 w-4 text-blue-900"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.5"
          >
            <path d="M4 13v4a2 2 0 0 0 2 2h1v-6h-1a2 2 0 0 0-2 2" />
            <path d="M20 13v4a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2" />
            <path d="M6 13v-1a6 6 0 0 1 12 0v1" />
          </svg>
          <span>Support</span>
        </div>
        <div className="flex items-center gap-4">
          <span className="whitespace-nowrap">Informed Delivery</span>
          <span className="whitespace-nowrap">Register / Sign In</span>
        </div>
      </div>
      {/* Mobile bar */}
      <div className="lg:hidden bg-white border-b border-gray-200 shadow-sm">
        <div className="flex items-center justify-between px-4 py-3">
          <button
            type="button"
            onClick={() => setMobileMenuOpen((prev) => !prev)}
            className="p-2 rounded-md border border-[#d8d8e0] text-[#1A2252]"
            aria-label="Toggle navigation"
          >
            <span className="block w-5 h-0.5 bg-[#1A2252] mb-1" />
            <span className="block w-5 h-0.5 bg-[#1A2252] mb-1" />
            <span className="block w-5 h-0.5 bg-[#1A2252]" />
          </button>
          <img
            src="/assets/logo_mobile.svg"
            alt="USPS Logo"
            className="h-8"
          />
            <button type="button" className="p-2 rounded-md text-[#1A2252]" aria-label="Search">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </button>
        </div>
        {mobileMenuOpen && (
          <div className="px-4 pb-6 bg-white border-t border-gray-200 text-sm text-[#1A2252] space-y-4">
            <div className="flex items-center justify-between pt-4">
              <button className="font-semibold">Quick Tools</button>
              <button className="text-[#c4122f] text-xs uppercase tracking-wide">FAQs</button>
            </div>
            <div className="flex flex-col gap-3 pt-2">
              {navItems.map((item) => (
                <a key={item} href="#" className="py-1 border-b border-gray-100">
                  {item}
                </a>
              ))}
            </div>
            <div className="pt-2 space-y-3 text-xs text-[#4a4f6d]">
              <div className="flex items-center gap-2">
                <svg className="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <circle cx="12" cy="12" r="9" />
                  <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
                </svg>
                <span>English</span>
              </div>
              <div className="flex items-center gap-2">
                <svg className="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M12 21s7-5.5 7-11a7 7 0 0 0-14 0c0 5.5 7 11 7 11z" />
                  <circle cx="12" cy="10" r="2.5" />
                </svg>
                <span>Locations</span>
              </div>
              <div className="flex items-center gap-2">
                <svg className="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M4 13v4a2 2 0 0 0 2 2h1v-6h-1a2 2 0 0 0-2 2" />
                  <path d="M20 13v4a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2" />
                  <path d="M6 13v-1a6 6 0 0 1 12 0v1" />
                </svg>
                <span>Support</span>
              </div>
              <div className="flex flex-col gap-1">
                <span className="font-semibold text-[#1A2252]">Informed Delivery</span>
                <span className="font-semibold text-[#1A2252]">Register / Sign In</span>
              </div>
            </div>
          </div>
        )}
      </div>
      {/* Desktop main nav */}
      <div className="hidden lg:flex max-w-[1180px] mx-auto items-center justify-between px-6">
        <div className="flex items-center gap-3 py-2 mr-6">
          <img
            src="https://www.usps.com/global-elements/header/images/utility-header/logo-sb.svg"
            alt="USPS Logo"
            className="h-8"
          />
        </div>
        <nav className="flex items-center">
          <ul className="flex items-center whitespace-nowrap text-sm font-medium text-[#1A2252]">
            <li className="relative">
              <a
                href="#"
                className="relative inline-flex items-center bg-[#1A2252] text-white px-8 pl-10 py-3 font-normal uppercase tracking-[0.08em] overflow-hidden"
                style={{
                  clipPath: "polygon(12px 0, 100% 0, calc(100% - 10px) 100%, 0% 100%)",
                }}
              >
                <span className="relative z-20">Quick Tools</span>
                <span
                  className="absolute top-0 right-[1px] h-full w-4 bg-[#c4122f]"
                  style={{
                    clipPath: "polygon(10px 0px, 100% 0px, 40% 100%, 0% 100%)",
                    zIndex: 15,
                  }}
                />
              </a>
            </li>
            {navItems.map((item) => (
              <li key={item} className="relative">
                <a href="#" className="flex items-center px-6 py-3 hover:bg-white transition-colors">
                  {item}
                </a>
              </li>
            ))}
          </ul>
        </nav>
        <div className="ml-6 p-2 cursor-pointer hover:bg-gray-300 rounded">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-5 w-5 text-[#1A2252]"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            strokeWidth="2"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>
      </div>
    </header>
  );
};

export default Header;
