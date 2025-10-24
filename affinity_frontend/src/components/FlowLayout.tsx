import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface FlowLayoutProps {
  children: React.ReactNode;
}

const FlowLayout: React.FC<FlowLayoutProps> = ({ children }) => {
  return (
    <div
      className="min-h-screen flex flex-col overflow-hidden bg-cover bg-center bg-no-repeat relative"
      style={{ backgroundImage: 'url(/assets/background.jpeg)' }}
    >
      <div className="absolute inset-0 bg-black/40" />
      <div className="relative z-10 flex flex-col min-h-screen">
        <Header />
        <main className="flex-1 flex items-center justify-center px-4 py-10">
          {children}
        </main>
        <Footer />
      </div>
    </div>
  );
};

export default FlowLayout;
