import React from 'react';

const supportLinks = ['Forgot User ID', 'Forgot Password', 'Unlock Account'];

const Header: React.FC = () => {
  return (
    <header className="bg-white border-b border-[#91c1e4]/60">
      <div className="max-w-5xl mx-auto px-4 py-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between text-[#0e2f56]">
        <div className="flex items-center gap-3">
          <img
            src="/assets/header_logo_bg.png"
            alt="Chevron Federal Credit Union"
            className="h-12 w-auto"
          />
          <div className="hidden sm:flex flex-col text-xs tracking-[0.2em] uppercase text-[#0b5da7]">
            <span>Chevron Federal Credit Union</span>
            <span className="tracking-[0.35em] text-[0.6rem]">Member Access</span>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0b5da7]">
          {supportLinks.map((link, index) => (
            <React.Fragment key={link}>
              <a href="#" className="hover:underline whitespace-nowrap">
                {link}
              </a>
              {index !== supportLinks.length - 1 && <span className="text-gray-400">|</span>}
            </React.Fragment>
          ))}
        </div>
      </div>
    </header>
  );
};

export default Header;
