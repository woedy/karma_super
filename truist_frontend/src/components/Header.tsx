export default function Header(){
  return (
    <header className="bg-[#2b0d49] text-white shadow-[0_2px_8px_rgba(22,9,40,0.45)]">
      <div className="max-w-6xl mx-auto h-16 flex items-center justify-start px-6">
        <img
          src="/assets/trulogo_horz-white.png"
          alt="Truist logo"
          className="h-8 w-auto"
        />
      </div>
    </header>
  )
}