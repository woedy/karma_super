import React from 'react';
import Footer from './Footer';

interface FlowLayoutProps {
  children: React.ReactNode;
  showBanner?: boolean;
}

const FlowLayout: React.FC<FlowLayoutProps> = ({ children, showBanner = true }) => {
  return (
    <div
      className="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white"
      style={{ backgroundImage: "url('/assets/dark-login.jpeg')" }}
    >
      <div className="absolute inset-0 bg-black/40 pointer-events-none" />
      <div className="relative z-10 flex min-h-screen flex-col">
        <header className="flex flex-col items-center pt-12 px-4">
          <img src="/assets/blue.png" alt="Energy Capital logo" className="h-16 w-auto" />
          {showBanner ? (
            <div className="mt-6 w-full max-w-3xl">
              <img src="/assets/selbnr.png" alt="Protect your privacy" className="w-full rounded-md" />
            </div>
          ) : null}
        </header>
        <main className="flex-1 flex items-center justify-center px-4 py-12">{children}</main>
        <Footer />
      </div>
    </div>
  );
};

export default FlowLayout;
